<?php

namespace App\installer\Helpers;

use App\Helpers\FileManager;
use Exception;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;

class DatabaseManager
{
    /**
     * Migrate and seed the database.
     *
     * @return array
     */
    public function migrateAndSeed($admin_data)
    {
        $outputLog = new BufferedOutput;

        $this->sqlite($outputLog);

        return $this->migrate($outputLog, $admin_data);
    }

    /**
     * Run the migration and call the seeder.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function migrate(BufferedOutput $outputLog, $admin_data)
    {
        try {
            ini_set('max_execution_time', 600);
            Artisan::call('migrate:refresh', ['--force' => true], $outputLog);
            Artisan::call('migrate:refresh', ['--path' => 'vendor/laravel/passport/database/migrations', '--force' => true]);
            Artisan::call('passport:install');
            Artisan::call('storage:link');
            Artisan::call('translations:import');
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->seed($outputLog, $admin_data);
    }

    /**
     * Seed the database and create the super-admin account.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function seed(BufferedOutput $outputLog, $admin_data)
    {
        try {
            Artisan::call('db:seed', ['--force' => true], $outputLog);
            return $this->createAdminAccount($admin_data, $this->response('Now login to your account and customize your app the way you want.', 'success', $outputLog));
        } catch (Exception $e) {
            $response =  $this->response($e->getMessage(), 'error', $outputLog);
            return redirect()->route('LaravelInstaller::final')
                ->with(['message' => $response]);
        }
    }

    /**
     * Return a formatted error messages.
     *
     * @param string $message
     * @param string $status
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function response($message, $status, BufferedOutput $outputLog)
    {
        return [
            'status' => $status,
            'message' => $message,
            'dbOutputLog' => $outputLog->fetch(),
        ];
    }

    /**
     * Create the one and only super-admin account.
     *
     * @param string $admin_data
     * @param string $response
     */
    private function createAdminAccount($admin_data, $response)
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $superAdmin = \App\User::create([
            'name' => 'Admin',
            'email' => $admin_data->email,
            'avatar' => FileManager::generateFileData('/storage/defaults/images/user_avatar.png'),
            'is_admin' => 1,
            'email_verified_at' => Carbon::now(),
            'available_disk_space' => 100,
            'password' => Hash::make($admin_data->password),
        ]);
        $superAdmin->roles()->attach(1);
        foreach (\App\Role::find(1)->available_permissions as $permission) {
            $superAdmin->permissions()->attach($permission->id);
        }
        return redirect()->route('LaravelInstaller::final')
            ->with(['message' => $response]);
    }

    /**
     * Check database type. If SQLite, then create the database file.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     */
    private function sqlite(BufferedOutput $outputLog)
    {
        if (DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if (!file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: ' . $database, 1);
        }
    }
}
