<?php

namespace App\Http\Controllers;

use App\Setting;
use Exception;

// use Illuminate\Support\Facades\Schema;

class UpgradeController extends Controller
{
    public function update($version)
    {
        if (file_exists(storage_path('installed'))) {
            if ($version == '3.3' && !Setting::get('software_version')) {
                try {
                    $file_contents = file_get_contents(base_path('.env'));
                    $file_contents = $file_contents . "\r\n\r\nMAILGUN_DOMAIN=\r\nMAILGUN_SECRET=";

                    file_put_contents(base_path('.env'), $file_contents);

                    Setting::set('software_version', '3.3', 0);
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                return redirect('/');
            }

            // reset cache
            \Artisan::call('route:clear');
            \Artisan::call('config:cache');
            \Artisan::call('cache:clear');

            return view('installer.updated');
        } else {
            return redirect('/install');
        }
    }

    // function recurseCopy(
    //     string $sourceDirectory,
    //     string $destinationDirectory,
    //     string $childFolder = ''
    // ): void {
    //     $directory = opendir($sourceDirectory);

    //     if (is_dir($destinationDirectory) === false) {
    //         mkdir($destinationDirectory);
    //     }

    //     if ($childFolder !== '') {
    //         if (is_dir("$destinationDirectory/$childFolder") === false) {
    //             mkdir("$destinationDirectory/$childFolder");
    //         }

    //         while (($file = readdir($directory)) !== false) {
    //             if ($file === '.' || $file === '..') {
    //                 continue;
    //             }

    //             if (is_dir("$sourceDirectory/$file") === true) {
    //                 recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
    //             } else {
    //                 copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
    //             }
    //         }

    //         closedir($directory);

    //         return;
    //     }

    //     while (($file = readdir($directory)) !== false) {
    //         if ($file === '.' || $file === '..') {
    //             continue;
    //         }

    //         if (is_dir("$sourceDirectory/$file") === true) {
    //             recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
    //         } else {
    //             copy("$sourceDirectory/$file", "$destinationDirectory/$file");
    //         }
    //     }

    //     closedir($directory);
    // }
}
