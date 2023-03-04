<?php

namespace App\Helpers\Manifest;

use App\Setting;

class ManifestGenerator
{
    public static function generate()
    {
        $replacements = [
            'SHORT_NAME' => config('app.name'),
            'NAME' => config('app.name'),
            'THEME_COLOR' => json_decode(Setting::get('themes'), true)['primary'],
            'BACKGROUND_COLOR' => json_decode(Setting::get('themes'), true)['light']['background'],
            'START_URL' => config('app.url'),
        ];

        @file_put_contents(
            public_path('manifest.json'),
            str_replace(
                array_keys($replacements),
                $replacements,
                file_get_contents(__DIR__.'/manifestExample.json')
            )
        );
    }
}
