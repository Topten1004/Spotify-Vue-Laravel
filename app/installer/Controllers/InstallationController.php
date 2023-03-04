<?php

namespace App\installer\Controllers;

use App\Helpers\FileManager;
use App\Helpers\Manifest\ManifestGenerator;
use App\installer\Events\LaravelInstallerFinished;
use App\NavigationItem;
use App\Page;
use App\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PlanTableSeeder;

class InstallationController extends Controller
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (file_exists($this->getEnvPath())) {
            return file_get_contents($this->envPath);
        } else {
            return '';
        }
    }

    /**
     * Create the .env file that contains the app enviroment data.
     */
    public function createEnvFile()
    {
        if (!file_exists($this->getEnvPath())) {
            copy($this->getEnvExamplePath(), $this->getEnvPath());
        } else {
            unlink($this->getEnvPath());
            copy($this->getEnvExamplePath(), $this->getEnvPath());
        }
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    public function createInstallationFile()
    {
        $installedLogFile = storage_path('installed');

        $dateStamp = date('Y/m/d h:i:sa');

        if (!file_exists($installedLogFile)) {
            $message = trans('installer_messages.installed.success_log_message') . $dateStamp . "\n";

            file_put_contents($installedLogFile, $message);
        } else {
            $message = trans('installer_messages.updater.log.success_message') . $dateStamp;

            file_put_contents($installedLogFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        return $message;
    }
    /**
     * Update installed file and display finished view.
     *
     * @param \App\installer\Helpers\InstalledFileManager $fileManager
     * @param \App\installer\Helpers\FinalInstallManager $finalInstall
     * @param \App\installer\Helpers\EnvironmentManager $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finishInstallation()
    {
        $this->createInstallationFile();

        event(new LaravelInstallerFinished);

        return response()->json('Installation completed successfully', 200);
    }

    public function install(Request $request)
    {
        // increase exuction time for slower servers
        ini_set('max_execution_time', 600);
    }


    // installation routes

    public function env(Request $request)
    {
        // create .env file & save variables
        try {
            $this->createEnv($request);
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function migrate(Request $request)
    {
        ini_set('max_execution_time', 600);
        try {
            // migrate database tables
            Artisan::call('migrate:refresh', ['--force' => true]);
            Artisan::call('migrate:refresh', ['--path' => 'vendor/laravel/passport/database/migrations', '--force' => true]);
            return response()->json([], 200);
        } catch (Exception $e) {
            return         dd('dsq');;
        }
    }

    public function dependencies(Request $request)
    {
        try {
            //install passport
            Artisan::call('passport:install');
            // // link the storage to the public directory
            // Artisan::call('storage:link');
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function translations(Request $request)
    {
        ini_set('max_execution_time', 600);
        try {
            // import the translations
            Artisan::call('translations:import');
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finish(Request $request)
    {
        try {
            //generate app key
            Artisan::call('key:generate', ['--force' => true]);
            // seed data
            Artisan::call('db:seed', ['--force' => true]);
            // survey
            $this->adaptApp($request);
            // generate manifest file
            ManifestGenerator::generate();
            //create admin account
            $this->createAdminAccount($request->admin);
            // finish installation
            $this->finishInstallation();

            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    private function formatValue($value)
    {
        if ($value === 0 || $value === false) $value = 'false';
        if ($value === 1 || $value === true) $value = 'true';
        if (!$value) $value = 'null';
        $value = trim($value);

        // wrap string in quotes, if it contains whitespace or special characters
        if (preg_match('/\s/', $value) || Str::contains($value, '#')) {
            //replace double quotes with single quotes
            $value = str_replace('"', "'", $value);

            //wrap string in quotes
            $value = '"' . $value . '"';
        }

        return $value;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function createEnv(Request $request)
    {
        $this->createEnvFile();
        $file_contents = file_get_contents($this->envPath);
        try {
            $results = 'Success';
            $file_contents = str_replace(
                'APP_KEY=',
                'APP_KEY=' . 'base64:' . base64_encode(Str::random(32)),
                $file_contents
            );
            $file_contents = str_replace(
                'APP_URL=',
                'APP_URL=' . $this->formatValue($request->config['app']['url']),
                $file_contents
            );
            $file_contents = str_replace(
                "APP_NAME=",
                "APP_NAME=" . $this->formatValue($request->config['app']['name']),
                $file_contents
            );
            $file_contents = str_replace(
                'APP_ENV=',
                'APP_ENV=production',
                $file_contents
            );
            $file_contents = str_replace(
                'DB_CONNECTION=',
                'DB_CONNECTION=' . $this->formatValue($request->config['database']['connection']),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_DATABASE=',
                'DB_DATABASE=' . $this->formatValue($request->config['database']['name']),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_HOST=',
                'DB_HOST=' . $this->formatValue($request->config['database']['host']),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_PORT=',
                'DB_PORT=' . $this->formatValue($request->config['database']['port']),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_USERNAME=',
                'DB_USERNAME=' . $this->formatValue($request->config['database']['username']),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_PASSWORD=',
                'DB_PASSWORD=' . $this->formatValue($request->config['database']['password']),
                $file_contents
            );
            file_put_contents($this->envPath, $file_contents);
        } catch (Exception $e) {
            $results = 'Errors';
        }
        return $results;
    }

    private function createAdminAccount($admin_data)
    {


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $superAdmin = \App\User::create([
            'name' => 'Admin',
            'email' => $admin_data['email'],
            'avatar' => FileManager::generateFileData('/storage/defaults/images/user_avatar.png'),
            'is_admin' => 1,
            'email_verified_at' => Carbon::now(),
            'available_disk_space' => 100,
            'password' => Hash::make($admin_data['password']),
        ]);
        $superAdmin->roles()->attach(1);
        foreach (\App\Role::find(1)->available_permissions as $permission) {
            $superAdmin->permissions()->attach($permission->id);
        }
        return 1;
    }

    private function adaptApp($request)
    {

        Setting::set('enablePodcasts', $request->survey['podcasts']);
        Setting::set('enable_artist_account', $request->survey['artists']);

        if ($request->survey['saas']) {
            Setting::set('saas', 1);
            Setting::set('default_currency', json_encode($request->survey['default_currency']));
            Setting::set('enable_subscription', $request->survey['subscription']);
            Setting::set('enable_selling', $request->survey['sell']);
            Setting::set('royalties', $request->survey['royalties']);
            // create store page if the user decided to sell
            if ($request->survey['sell']) {
                Page::create([
                    'name' => 'Store',
                    'title' => 'Store',
                    'description' => 'Explore our amazing tracks & choose your license',
                    'icon' => 'shopping',
                    'showLinkOnTheRightSidebar' => 0,
                    'blank' => 0,
                    'path' => '/store',
                ]);
                NavigationItem::create([
                    'name' => 'Store',
                    'icon' => 'shopping',
                    'visibility' => 1,
                    'custom' => 0,
                    'rank' => 4,
                    'path' => '/store'
                ]);
            }

            // create subscription page if the user decided to offre subscriptions
            if ($request->survey['subscription']) {
                NavigationItem::create([
                    'name' => 'Subscription',
                    'icon' => 'star-circle',
                    'visibility' => 1,
                    'custom' => 0,
                    'rank' => 5,
                    'path' => '/subscription'
                ]);
            }
            if ($request->survey['royalties']) {
                Setting::set('artist_royalty', $request->survey['artist_royalty']);
            }

            if ($request->survey['sell'] && $request->survey['artists']) {
                Setting::set('artist_sale_cut', $request->survey['artist_sale_cut']);
            }
        } else {
            Setting::set('saas', 0);
            Setting::set('enable_subscription', 0);
            Setting::set('enable_selling', 0);
            Setting::set('royalties', 0);
        }


        if (!$request->survey['podcasts']) {
            $navigation_item = NavigationItem::find(3);
            $navigation_item->visibility = 0;
            $navigation_item->save();
        }
    }
}
