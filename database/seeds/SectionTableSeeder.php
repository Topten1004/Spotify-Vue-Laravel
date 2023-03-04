<?php

use Illuminate\Database\Seeder;

use App\Section;

class SectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Section::truncate();
        // seeding the default sections of the charts page ( charts id = 1)
        Section::create([
            'title' => 'New releases',
            'endpoint' => 'new-releases',
            'nb_labels' => 10,
            'page_id' => 1,
            'rank' => 0
        ]);
        Section::create([
            'title' => 'Most Played Songs',
            'endpoint' => 'popular-songs',
            'nb_labels' => 10,
            'page_id' => 1,
            'rank' => 1
        ]);
        Section::create([
            'title' => 'Most Liked Songs',
            'endpoint' => 'most-liked-songs',
            'nb_labels' => 10,
            'page_id' => 1,
            'rank' => 2
        ]);

        Section::create([
            'title' => 'Most Played Albums',
            'endpoint' => 'popular-albums',
            'nb_labels' => 10,
            'page_id' => 1,
            'rank' => 3
        ]);
        Section::create([
            'title' => 'Most Played Podcasts',
            'endpoint' => 'popular-podcasts',
            'nb_labels' => 10,
            'page_id' => 1,
            'rank' => 4
        ]);
        Section::create([
            'title' => 'Latest Podcasts',
            'endpoint' => 'latest-podcasts',
            'nb_labels' => 10,
            'page_id' => 1,
            'rank' => 5
        ]);
        // home page 
        Section::create([
            'title' => 'New releases',
            'endpoint' => 'new-releases',
            'nb_labels' => 10,
            'page_id' => 2,
            'rank' => 0
        ]);
    }
}
