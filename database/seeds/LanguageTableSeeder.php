<?php

use App\Language;
use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::truncate();

        Language::create([
            'name' => 'English',
            'locale' => 'en',
            'status' => 1,
            'isDefault' => 1,
            'flag' => 'usa',
            'isDeletable' => 0
        ]);
        Language::create([
            'name' => 'Español',
            'locale' => 'es',
            'status' => -1,
            'flag' => 'spain',
            'isDeletable' => 0
        ]);
        Language::create([
            'name' => 'Français',
            'locale' => 'fr',
            'status' => -1,
            'flag' => 'france',
            'isDeletable' => 0
        ]);
        Language::create([
            'name' => 'Deutsch',
            'locale' => 'de',
            'status' => -1,
            'flag' => 'germany',
            'isDeletable' => 0
        ]);
        Language::create([
            'name' => 'العربية',
            'locale' => 'ar',
            'status' => -1,
            'flag' => 'uae',
            'isDeletable' => 0
        ]);
        Language::create([
            'name' => 'हिंदी',
            'locale' => 'hi',
            'status' => -1,
            'flag' => 'india',
            'isDeletable' => 0
        ]);
        Language::create([
            'name' => 'Italiano',
            'locale' => 'it',
            'status' => -1,
            'flag' => 'italy',
            'isDeletable' => 0
        ]);
    }
}
