<?php

use Illuminate\Database\Seeder;

use App\Genre;
use App\Helpers\FileManager;

class GenreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Genre::truncate();

        // seeding the default genres
        Genre::create([
            'name' => 'Electronic',
            'slug' => 'electronic',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/electronic.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/dj.svg')
        ]);
        Genre::create([
            'name' => 'Sound FX',
            'slug' => 'sound-fx',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/sound-fx.jpg'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/noise.svg')
        ]);
        Genre::create([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/gaming.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/controller.svg')
        ]);
        Genre::create([
            'name' => 'Piano',
            'slug' => 'piano',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/piano.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/piano.svg')
        ]);
        Genre::create([
            'name' => 'Chill',
            'slug' => 'chill',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/chill.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/relax.svg')
        ]);
        Genre::create([
            'name' => 'Jazz',
            'slug' => 'jazz',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/jazz.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/horn.svg')
        ]);
        Genre::create([
            'name' => 'K Pop',
            'slug' => 'k-pop',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/k-pop.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/headphone.svg')
        ]);
        Genre::create([
            'name' => 'Classical',
            'slug' => 'classical',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/classical.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/guitar.svg')
        ]);
        Genre::create([
            'name' => 'Hip Hop',
            'slug' => 'hip-hop',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/hip-hop.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/radio.svg')
        ]);
        Genre::create([
            'name' => 'Indie',
            'slug' => 'indie',
            'cover' => FileManager::generateFileData('/storage/defaults/images/genres/indie.png'),
            'icon' => FileManager::generateFileData('/storage/defaults/icons/microphone.svg')
        ]);
    }
}
