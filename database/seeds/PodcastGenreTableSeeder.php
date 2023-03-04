<?php

use App\Helpers\FileManager;
use Illuminate\Database\Seeder;
use App\PodcastGenre;
class PodcastGenreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PodcastGenre::truncate();


        // default podcast genres 
        PodcastGenre::create([
            'name' => 'News',
            'slug' => 'news',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-news.png'),
            'listen_notes_genre_id' => 99
        ]);
        PodcastGenre::create([
            'name' => 'Arts',
            'slug' => 'arts',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-art.png'),
            'listen_notes_genre_id' => 100
        ]);
        PodcastGenre::create([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-gaming.png'),
        ]);
        PodcastGenre::create([
            'name' => 'Education',
            'slug' => 'education',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-education.png'),
            'listen_notes_genre_id' => 111
        ]);
        PodcastGenre::create([
            'name' => 'Comedy',
            'slug' => 'comedy',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-comedy.png'),
            'listen_notes_genre_id' => 133
        ]);
        PodcastGenre::create([
            'name' => 'Health',
            'slug' => 'health',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-health.png'),
            'listen_notes_genre_id' => 88
        ]);

        PodcastGenre::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-fiction.png'),
            'listen_notes_genre_id' => 168
        ]);
        PodcastGenre::create([
            'name' => 'Sports',
            'slug' => 'sports',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-sports.png'),
            'listen_notes_genre_id' => 77
        ]);

        PodcastGenre::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-technology.png'),
            'listen_notes_genre_id' => 127
        ]);
        PodcastGenre::create([
            'name' => 'Science',
            'slug' => 'science',
            'cover' => FileManager::generateFileData('/storage/defaults/images/podcast_genres/podcast-science.png'),
            'listen_notes_genre_id' => 93
        ]);

    }
}
