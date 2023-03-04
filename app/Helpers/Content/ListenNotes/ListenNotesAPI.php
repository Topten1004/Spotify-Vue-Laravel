<?php

namespace App\Helpers\Content\ListenNotes;

use App\Http\Resources\ListenNotes\PodcastResource;
use App\Http\Resources\ListenNotes\PodcastSearchResource;
use Illuminate\Support\Facades\Cache;

class ListenNotesAPI{

    private $client = null;
    
    public function __construct($client){
        $this->client = $client;
    }

    public function podcasts($query)
    {
        $response = Cache::remember('listen_notes_search_' . $query, 244800, function() use ($query){
            return $this->client->search( [ 'q' => $query ] );
        });
        return collect(json_decode($response)->results);
    }

    public function podcast($podcast_id)
    {
        $response = Cache::remember('listen_notes_podcast_' . $podcast_id, 244800, function() use ($podcast_id){
            return $this->client->fetchPodcastById( [ 'id' => $podcast_id ] );
        });
        return json_decode($response);
    }

    public function podcastsWithIDS($ids)
    {
        $response = Cache::remember('listen_notes_podcasts_' . $ids , 244800, function() use ($ids){
            return $this->client->batchFetchPodcasts( [ 'ids' => $ids ] );
        });
        return json_decode($response);
    }

    public function episode($episode_id)
    {
        $response = Cache::remember('listen_notes_episode_' . $episode_id, 244800, function() use ($episode_id){
            return $this->client->fetchEpisodeById( [ 'id' => $episode_id ] );
        });
        return json_decode($response);
    }

    public function bestPodcasts()
    {
        $response = Cache::remember('listen_notes_best_podcasts', 244800, function(){
            return $this->client->fetchBestPodcasts([]);
        });
        return  json_decode($response)->podcasts;
    }

    public function genrePodcasts($genre_id)
    {
        $response = Cache::remember('listen_notes_genre_' . $genre_id, 244800, function() use ($genre_id){
            return $this->client->fetchBestPodcasts(['genre_id' => $genre_id]);
        });
        return  json_decode($response)->podcasts;
    }

}