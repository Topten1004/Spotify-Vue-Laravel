<?php

use Illuminate\Database\Seeder;

use App\NavigationItem;

class NavigationItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NavigationItem::truncate();
        // seeding the default Navigation Items
        NavigationItem::create([
            'name' => 'Home',
            'icon' => 'home',
            'visibility' => 1,
            'page_id' => 1,
            'custom' => 1,
            'rank' => 1,
            'path' => '/home',
            'navigatesTo' => 'Custom page'
        ]);
        NavigationItem::create([
            'name' => 'Browse',
            'icon' => 'compass',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 2,
            'path' => '/browse'
        ]);
        NavigationItem::create([
            'name' => 'Podcasts',
            'icon' => 'microphone',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 3,
            'path' => '/podcasts'
        ]);
        NavigationItem::create([
            'name' => 'Library',
            'icon' => 'music-box-multiple',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 5,
            'path' => '/library/my-songs'
        ]);
    }
}
