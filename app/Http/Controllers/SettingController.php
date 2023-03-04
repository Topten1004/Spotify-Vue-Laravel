<?php

namespace App\Http\Controllers;

use Spotify;
use App\Exceptions\FEException;
use App\Helpers\Billing\PayPalAPI;
use App\Helpers\Billing\StripeAPI;
use App\Language;
use App\Mail\testMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use FileManager;
use App\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Get all the settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Setting::all();
    }
    /**
     * Get the themes.
     *
     * @return \Illuminate\Http\Response
     */
    public function themes()
    {
        return Setting::get('themes');
    }
    /**
     * Update & save the mail settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveMailSettings(Request $request)
    {
        if ($request->enable_mail) {

            if ($request->mailMailer === 'smtp') {
                $request->validate([
                    'host' => 'required',
                    'port' => 'required',
                    'username' => 'required',
                    'encryption' => 'required',
                    'password' => 'required',
                    'from_address' => 'required',
                    'from_name' => 'required'
                ]);
            } else if ($request->mailMailer === 'mailgun') {
                $request->validate([
                    'mailgunAPIKey' => 'required',
                    'mailgunURL' => 'required',
                    'from_address' => 'required',
                    'from_name' => 'required'
                ]);
            }

            Artisan::call('config:clear');
            // Setting::set('contactEmailAddress', $request->contact_email_address, 0);
            // testing the mail credentials by sending a dummy mail 
            try {

                if ($request->mailMailer === 'smtp') {
                    config(['mail.default' =>  'smtp']);
                    config(['mail.mailers.smtp.host' =>  $request->host]);
                    config(['mail.mailers.smtp.username' =>  $request->username]);
                    config(['mail.mailers.smtp.password' =>  $request->password]);
                    config(['mail.mailers.smtp.port' =>  $request->port]);
                    config(['mail.mailers.smtp.encryption' =>  $request->encryption]);
                } else if ($request->mailMailer === 'mailgun') {
                    config(['mail.default' =>  'mailgun']);
                    config(['mail.mailers.mailgun.secret'  =>  $request->mailgunAPIKey]);
                    config(['mail.mailers.mailgun.domain' =>  $request->mailgunURL]);
                }

                config(['mail.from.address' =>  $request->from_address]);
                config(['mail.from.name' =>  $request->from_name]);

                Mail::to($request->from_address)->send(new testMail());
            } catch (\Exception $exception) {
                throw new FEException(__('Failed to connect to the email provider.') . ' ' .  __('Make sure your information is correct.'), $exception->getMessage(), 420);
            }
            // if the email was sent seccessfully then the app data will be updated
            // on the .env file
            $path = base_path('.env');
            $content = file_get_contents($path);
            if ($request->mailMailer === 'smtp') {
                $content = preg_replace('/MAIL_PASSWORD=.*/', 'MAIL_PASSWORD=' . $this->formatValue($request->password), $content);
                $content = preg_replace('/MAIL_USERNAME=.*/', 'MAIL_USERNAME=' . $this->formatValue($request->username), $content);
                $content = preg_replace('/MAIL_HOST=.*/', 'MAIL_HOST=' . $this->formatValue($request->host), $content);
                $content = preg_replace('/MAIL_PORT=.*/', 'MAIL_PORT=' . $this->formatValue($request->port), $content);
                $content = preg_replace('/MAIL_ENCRYPTION=.*/', 'MAIL_ENCRYPTION=' . $this->formatValue($request->encryption), $content);
            } else if ($request->mailMailer === 'mailgun') {
                $content = preg_replace('/MAILGUN_DOMAIN=.*/', 'MAILGUN_DOMAIN=' . $this->formatValue($request->mailgunURL), $content);
                $content = preg_replace('/MAILGUN_SECRET=.*/', 'MAILGUN_SECRET=' . $this->formatValue($request->mailgunAPIKey), $content);
            }

            $content = preg_replace('/MAIL_MAILER=.*/', 'MAIL_MAILER=' . $this->formatValue($request->mailMailer), $content);
            $content = preg_replace('/MAIL_FROM_ADDRESS=.*/', 'MAIL_FROM_ADDRESS=' . $this->formatValue($request->from_address), $content);
            $content = preg_replace('/MAIL_FROM_NAME=.*/', 'MAIL_FROM_NAME=' . $this->formatValue($request->from_name), $content);
            file_put_contents($path, $content);
            // saving the data on the database
            Setting::set('enableMail', 1);
            Setting::set('mailMailer', $request->mailMailer, 0);
            if ($request->mailMailer === 'smtp') {
                Setting::set('mailHost', $request->host, 0);
                Setting::set('mailPort', $request->port, 0);
                Setting::set('mailUsername', $request->username, 0);
                Setting::set('mailPassword', $request->password, 0);
                Setting::set('mailEncryption', $request->encryption, 0);
            } else if ($request->mailMailer === 'mailgun') {
                // mailgun
                Setting::set('mailgunAPIKey', $request->mailgunAPIKey, 0);
                Setting::set('mailgunURL', $request->mailgunURL, 0);
            }


            Setting::set('mailFromAddress', $request->from_address, 0);
            Setting::set('mailFromName', $request->from_name, 0);
        } else {
            Setting::set('enableMail', 0);
        }
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['MESSAGE' => 'SUCCESS'], 200);
    }
    /**
     * Update & save the mail settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveBillingSettings(Request $request)
    {
        if ($request->enable_billing) {
            Setting::set('default_currency', $request->default_currency);
            Artisan::call('config:clear');
            if ($request->stripe_gateway) {
                $request->validate([
                    'stripe_client_id' => 'required|string',
                    'stripe_secret' => 'required|string',
                ]);
                // testing connection
                try {
                    config(['services.stripe.key' =>  $request->stripe_client_id]);
                    config(['services.stripe.secret' => $request->stripe_secret]);
                    $stripeClient = new StripeAPI();
                    // fetching products as an example
                    // $stripe->products->all();
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    throw new FEException(__("Authentication with Stripe's API failed"), $e->getMessage(), 500);
                    // (maybe you changed API keys recently)
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    throw new FEException(__("Network communication with Stripe failed"), $e->getMessage(), 500);
                } catch (\Exception $e) {
                    throw $e;
                }
                // synchnorize plans on the stripe gateway if they do not exist
                $stripeClient->synchnorizePlans();
                // if passes save the settings
                $path = base_path('.env');
                $content = file_get_contents($path);
                $content = preg_replace('/STRIPE_KEY=.*/', 'STRIPE_KEY=' . $this->formatValue($request->stripe_client_id), $content);
                $content = preg_replace('/STRIPE_SECRET=.*/', 'STRIPE_SECRET=' . $this->formatValue($request->stripe_secret), $content);
                file_put_contents($path, $content);
                // saving the data on the database
                Setting::set('stripeClientID', $request->stripe_client_id, 1);
                Setting::set('stripeSecret', $request->stripe_secret, 0);
            }

            if ($request->paypal_gateway) {
                $request->validate([
                    'paypal_client_id' => 'required|string',
                    'paypal_secret' => 'required|string',
                ]);
                // testing connection
                try {
                    config(['services.paypal.key' =>  $request->paypal_client_id]);
                    config(['services.paypal.secret' => $request->paypal_secret]);
                    config(['services.paypal.sandbox' => $request->paypal_sandbox_mode]);
                    $paypalClient = new PayPalAPI();
                } catch (\Exception $e) {
                    throw new FEException(__("Failed to connect to PayPal's API"), $e->getMessage(), 500);
                }
                // if passes save the settings
                $path = base_path('.env');
                $content = file_get_contents($path);

                $content = preg_replace('/PAYPAL_CLIENT_ID=.*/', 'PAYPAL_CLIENT_ID=' . $request->paypal_client_id, $content);
                $content = preg_replace('/PAYPAL_SECRET_ID=.*/', 'PAYPAL_SECRET_ID=' . $request->paypal_secret, $content);
                $content = preg_replace('/PAYPAL_SANDBOX=.*/', 'PAYPAL_SANDBOX=' . $request->paypal_sandbox_mode, $content);
                // synchnorize plans on the stripe gateway if they do not exist
                $paypalClient->synchnorizePlans();
                file_put_contents($path, $content);
                // saving the data on the database
                Setting::set('paypalSandboxMode',  $request->paypal_sandbox_mode);
                Setting::set('paypalClientID', $request->paypal_client_id, 1);
                Setting::set('paypalSecret', $request->paypal_secret, 0);
            }
        }
        Setting::set('enableBilling', $request->enable_billing);
        Setting::set('stripeGateway', $request->stripe_gateway);
        Setting::set('paypalGateway', $request->paypal_gateway);
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Update & save the storage settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveStorageSettings(Request $request)
    {
        $disk = json_decode($request->storageDisk)->disk;
        if ($disk === "s3") {
            // test the connection
            Artisan::call('config:clear');
            $endpoint = preg_match('/http|https/', $request->S3Endpoint) ? $request->S3Endpoint : 'https://' . $request->S3Endpoint;
            config([
                'filesystems.disks.s3.bucket' => $request->S3Bucket,
                'filesystems.disks.s3.credentials.key' => $request->S3Key,
                'filesystems.disks.s3.credentials.secret' => $request->S3Secret,
                'filesystems.disks.s3.region' => $request->S3Region,
                'filesystems.disks.s3.endpoint' => $endpoint
            ]);
            try {
                Storage::disk('s3')->put('test_file.txt', 'test');
                Storage::disk('s3')->delete('test_file.txt');
            } catch (\Exception $e) {
                throw new FEException(__('Failed to connect with the S3 API.') . ' ' .  __('Make sure your information is correct.'), $e->getMessage(), 500);
            }
            $path = base_path('.env');
            $content = file_get_contents($path);
            $content  = preg_replace('/AWS_ACCESS_KEY_ID=.*/', 'AWS_ACCESS_KEY_ID=' . $this->formatValue($request->S3Key), $content);
            if ($request->S3Secret) {
                $content  = preg_replace('/AWS_SECRET_ACCESS_KEY=.*/', 'AWS_SECRET_ACCESS_KEY=' . $this->formatValue($request->S3Secret), $content);
            }
            $content  = preg_replace('/AWS_DEFAULT_REGION=.*/', 'AWS_DEFAULT_REGION=' . $this->formatValue($request->S3Region), $content);
            $content  = preg_replace('/AWS_ENDPOINT=.*/', 'AWS_ENDPOINT=' . $this->formatValue($endpoint), $content);
            $content  = preg_replace('/AWS_BUCKET=.*/', 'AWS_BUCKET=' . $this->formatValue($request->S3Bucket), $content);
            file_put_contents($path, $content);
            Setting::set('S3Key', $request->S3Key, 0);
            Setting::set('S3Secret', $request->S3Secret, 0);
            Setting::set('S3Region', $request->S3Region, 0);
            Setting::set('S3Bucket', $request->S3Bucket, 0);
            Setting::set('S3Endpoint', $request->S3Endpoint, 0);
        }
        if ($disk === "b2") {
            $endpoint = preg_match('/http|https/', $request->blackblazeEndpoint) ? $request->blackblazeEndpoint : 'https://' . $request->blackblazeEndpoint;
            // test the connection
            Artisan::call('config:clear');
            config([
                'filesystems.disks.b2.accountId' => $request->blackblazeKeyID,
                'filesystems.disks.b2.applicationKey' => $request->blackblazeApplicationKey,
                'filesystems.disks.b2.bucketId' => $request->blackblazeBucketID,
                'filesystems.disks.b2.endpoint' => $endpoint
            ]);
            // dd(config());
            try {
                Storage::disk('b2')->put('test_file.txt', 'test');
                Storage::disk('b2')->delete('test_file.txt');
            } catch (\Exception $e) {
                throw new FEException(__('Failed to connect with Blackblaze B2 API.') . ' ' . __('Make sure your information is correct.'), $e->getMessage(), 500);
            }
            $path = base_path('.env');
            $content = file_get_contents($path);
            $content  = preg_replace('/BLACKBLAZE_KEY_ID=.*/', 'BLACKBLAZE_KEY_ID=' . $this->formatValue($request->blackblazeKeyID), $content);
            $content  = preg_replace('/BLACKBLAZE_APPLICATION_KEY=.*/', 'BLACKBLAZE_APPLICATION_KEY=' . $this->formatValue($request->blackblazeApplicationKey), $content);
            $content  = preg_replace('/BLACKBLAZE_BUCKET_ID=.*/', 'BLACKBLAZE_BUCKET_ID=' . $this->formatValue($request->blackblazeBucketID), $content);
            $content  = preg_replace('/BLACKBLAZE_BUCKET_NAME=.*/', 'BLACKBLAZE_BUCKET_NAME=' . $this->formatValue($request->blackblazeBucketName), $content);
            $content  = preg_replace('/BLACKBLAZE_ENDPOINT=.*/', 'BLACKBLAZE_ENDPOINT=' . $this->formatValue($endpoint), $content);
            file_put_contents($path, $content);
            Setting::set('blackblazeKeyId', $request->blackblazeKeyID, 0);
            Setting::set('blackblazeApplicationKey', $request->blackblazeApplicationKey, 0);
            Setting::set('blackblazeBucketID', $request->blackblazeBucketID, 0);
            Setting::set('blackblazeBucketName', $request->blackblazeBucketName, 0);
            Setting::set('blackblazeEndpoint', $request->blackblazeEndpoint, 0);
        }
        Setting::set('storageDisk', $request->storageDisk);
        Setting::set('maxImageSize', $request->maxImageSize);
        Setting::set('maxFileSize', $request->maxFileSize);
        Setting::set('availableUserDiskSpace', $request->availableUserDiskSpace);
        Setting::set('availableArtistDiskSpace', $request->availableArtistDiskSpace);
        Artisan::call('config:clear');
        //Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['MESSAGE' => 'SUCCESS'], 200);
    }
    /**
     * Update all the settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSettings(Request $request)
    {
        $path = base_path('.env');
        foreach ($request->settings as $key => $value) {
            if ($key  === 'pusherKey') {
                file_put_contents($path, str_replace(
                    'PUSHER_APP_KEY=' . config('broadcasting.connections.pusher.key'),
                    'PUSHER_APP_KEY=' . $this->formatValue($value),
                    file_get_contents($path)
                ));
                Setting::set($key, $value, 1);
            } else
            if ($key  === 'pusherSecret') {
                file_put_contents($path, str_replace(
                    'PUSHER_APP_SECRET=' . config('broadcasting.connections.pusher.secret'),
                    'PUSHER_APP_SECRET=' . $this->formatValue($value),
                    file_get_contents($path)
                ));
                Setting::set($key, $value, 0);
            } else
            
            if ($key  === 'pusherAppId') {
                file_put_contents($path, str_replace(
                    'PUSHER_APP_ID=' . config('broadcasting.connections.pusher.app_id'),
                    'PUSHER_APP_ID=' . $this->formatValue($value),
                    file_get_contents($path)
                ));
                Setting::set($key, $value, 0);
            } else
            if ($key  === 'pusherCluster') {
                file_put_contents($path, str_replace(
                    'PUSHER_APP_CLUSTER=' . config('broadcasting.connections.pusher.options.cluster'),
                    'PUSHER_APP_CLUSTER=' . $this->formatValue($value),
                    file_get_contents($path)
                ));
                Setting::set($key, $value, 1);
            } else {
                Setting::set($key, $value);
            }
        }
        // update locale
        Language::where('isDefault', 1)->where('locale', '!=', Setting::get('locale'))->update(['isDefault' => 0]);
        $lang = Language::where('locale', Setting::get('locale'))->first();
        $lang->isDefault = 1;
        $lang->save();
        app()->setLocale($lang->locale);
        // update the lang of the user
        auth()->user()->lang = $lang->locale;
        auth()->user()->save();
        Setting::get('requireAuth') == 0 ? Setting::set('requireEmailConfirmation', 0) : '';
        Setting::get('enableFriendshipSystem') == 0 ? Setting::set('enableChat', 0) : '';
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Save ads code & settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAds(Request $request)
    {
        Setting::set('rect_ad', $request->rect_ad);
        Setting::set('square_ad', $request->square_ad);
        Setting::set('enableAds', $request->enableAds);

        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Save ads code & settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProviders(Request $request)
    {
        if ($request->provider_spotify) {
            $request->validate([
                'spotifyClientID' => 'required',
                'spotifyClientSecret' => 'required'
            ]);
            Artisan::call('config:clear');
            // testing the given credentials if they work
            try {
                config(['spotify.auth.client_id' =>  $request->spotifyClientID]);
                config(['spotify.auth.client_secret' =>  $request->spotifyClientSecret]);
                Spotify::newReleases()->limit(1)->get();
            } catch (\Exception $exception) {
                throw new FEException(__('Failed to connect to Spotify API.') . ' ' .  __('Make sure your information is correct.'), $exception->getMessage(), 500);
            }
            // if the email was sent seccessfully then the app data will be updated
            // on the .env file
            $path = base_path('.env');
            $content = file_get_contents($path);
            $content = preg_replace('/SPOTIFY_CLIENT=.*/', 'SPOTIFY_CLIENT=' .  $request->spotifyClientID, $content);
            $content = preg_replace('/SPOTIFY_SECRET=.*/', 'SPOTIFY_SECRET=' . $request->spotifyClientSecret, $content);
            file_put_contents($path, $content);
            // saving the data on the database
            Setting::set('provider_spotify', 1);
            Setting::set('spotifyClientID', $request->spotifyClientID, 0);
            Setting::set('spotifyClientSecret', $request->spotifyClientSecret, 0);
            Setting::set('spotify_search', $request->spotify_search, 0);
            Setting::set('show_external_link_spotify', $request->show_external_link_spotify, 1);
        } else {
            Setting::set('provider_spotify', 0);
        }

        if ($request->provider_listenNotes) {
            $request->validate([
                'listenNotesClientID' => 'required'
            ]);
            Artisan::call('config:clear');
            // testing the given credentials if they work
            try {
                config(['listenNotes.client_id' =>  $request->listenNotesClientID]);

                // ListenNOtes::newReleases()->limit(1)->get();
            } catch (\Exception $exception) {
                throw new FEException(__('Failed to connect to Spotify API.') . ' ' .  __('Make sure your information is correct.'), $exception->getMessage(), 500);
            }
            // if the email was sent seccessfully then the app data will be updated
            // on the .env file
            $path = base_path('.env');
            $content = file_get_contents($path);
            $content = preg_replace('/LISTENNOTES_CLIENT_ID=.*/', 'LISTENNOTES_CLIENT_ID=' .  $request->listenNotesClientID, $content);
            file_put_contents($path, $content);
            // saving the data on the database
            Setting::set('provider_listenNotes', 1);
            Setting::set('listenNotes_search', $request->listenNotes_search, 0);
            Setting::set('listenNotesClientID', $request->listenNotesClientID, 0);
        } else {
            Setting::set('provider_listenNotes', 0);
        }
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['MESSAGE' => 'SUCCESS'], 200);
    }
    /**
     * Update & save the themes.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveThemes(Request $request)
    {
        Setting::set('themes', json_encode($request->themes));
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        Cache::put('themes', json_decode(Setting::get('themes'), true), 7200);
        return response()->json(['message' => 'SUCCESS'], 200);
    }


    public function saveAnalyticsSettings(Request $request)
    {
        Setting::set('ga4', $request->ga4);

        Setting::set('ga4MID', $request->ga4MID);

        if (!$request->ga4) {
            Setting::set('analytics_play_event', 0);
            Setting::set('analytics_download_event', 0);
            Setting::set('analytics_like_event', 0);
            Setting::set('analytics_adClick_event', 0);
            Setting::set('analytics_fileUpload_event', 0);
            Setting::set('analytics_chat_event', 0);
            Setting::set('analytics_login_event', 0);
            Setting::set('analytics_logout_event', 0);
            Setting::set('analytics_subscription_event', 0);
        } else {
            Setting::set('analytics_play_event', $request->analytics_play_event);
            Setting::set('analytics_download_event', $request->analytics_download_event);
            Setting::set('analytics_like_event', $request->analytics_like_event);
            Setting::set('analytics_adClick_event', $request->analytics_adClick_event);
            Setting::set('analytics_fileUpload_event', $request->analytics_fileUpload_event);
            Setting::set('analytics_chat_event', $request->analytics_chat_event);
            Setting::set('analytics_login_event', $request->analytics_login_event);
            Setting::set('analytics_purchase_event', $request->analytics_purchase_event);
            // Setting::set('analytics_logout_event', $request->analytics_logout_event);
            Setting::set('analytics_subscription_event', $request->analytics_subscription_event);
        }
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Reset the settings to the default.
     * @return \Illuminate\Http\Response
     */
    public function resetSettings()
    {
        return response()->json(Setting::default(), 200);
    }
    /**
     * Update all the appearance general settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAppearanceGeneralSettings(Request $request)

    {
        if ($request->file('logo')) {
            $newLogo = FileManager::asset_path(FileManager::update($request->file('logo'), Setting::get('appLogo'), '/'));
            Setting::set('appLogo', $newLogo);
        } else {
            Setting::set('appLogo', $request->logo);
        }
        if ($request->file('favicon')) {
            Setting::set('appFavicon', FileManager::asset_path(FileManager::update($request->file('favicon'), Setting::get('appFavicon'), '/')));
        } else {
            Setting::set('appFavicon', $request->favicon);
        }
        // if ($request->file('favicon')) {
        //     Setting::set('appFavicon', FileManager::asset_path(FileManager::update($request->file('favicon'), Setting::get('appfavicon'), '/')));
        // } else {
        //     Setting::set('appFavicon', $request->favicon);
        // }
        Setting::set('playerLanding', $request->playerLanding);
        Setting::set('hideRightSidebar', $request->hideRightSidebar);
        Setting::set('enableThemeSwitcher', $request->enableThemeSwitcher);
        Setting::set('defaultTheme', $request->defaultTheme);
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Update all the appearance general settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePlayerSettings(Request $request)

    {
        Setting::set('disableVideo', $request->disableVideo);
        Setting::set('hideDownloadButton', $request->hideDownloadButton);
        Setting::set('playerVolume', $request->playerVolume);
        
        
        if ($request->file('cover')) {
            Setting::set('adsCover', FileManager::update($request->file('cover'), Setting::get('adsCover'), '/ads/cover/'));
        }
        
        if ($request->file('audio')) {
            Setting::set('adsAudio', FileManager::update($request->file('audio'), Setting::get('adsAudio'), '/ads/audio/'));
        }
        
        Setting::set('adsTitle', $request->adsTitle);
        
        if ($request->autoPlay) {
            Setting::set('autoPlay', 1);
            Setting::set('crossfade', $request->crossfade);
            Setting::set('shuffleOrder', $request->shuffleOrder);
        } else {
            Setting::set('autoPlay', 0);
            Setting::set('crossfade', 0);
        }
        Cache::put('settings', Setting::getPublicSettings(), 7200);

        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Update all the landing page settings.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveLandingPageSettings(Request $request)
    {
        $data = $request->all();
        if ($request->enableLandingPage) {
            $old_setttings = json_decode(Setting::get('landingPage'));
            if ($request->file('headerBackground')) {
                $data['headerBackground'] =  FileManager::asset_path(FileManager::update($request->headerBackground, $old_setttings->headerBackground, '/landing/'));
            }
            if ($request->file('footerBackground')) {
                $data['footerBackground'] =  FileManager::asset_path(FileManager::update($request->footerBackground, $old_setttings->footerBackground, '/landing/'));
            }
            // saving data at database
            Setting::set('landingPage', json_encode($data));
        }
        Setting::set('enableLandingPage', $request->enableLandingPage);
        Cache::put('settings', Setting::getPublicSettings(), 7200);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Upload a landing page section image.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadSectionBackground(Request $request)
    {
        $background = $request->background;
        if ($request->file('background')) {
            $background = FileManager::asset_path(FileManager::store($request, '/landing/', 'background'));
        }
        return response()->json(['title' => $request->title, 'description' => $request->description, 'background' => $background], 200);
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
}
