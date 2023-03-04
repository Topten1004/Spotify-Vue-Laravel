<?php

namespace App\Traits;

use App\Helpers\Content\ListenNotes\ListenNotes;
use App\Helpers\Content\ListenNotes\ListenNotesAPI;
use DB;
use Spotify;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\ListenNotes\PodcastResource as ListenNotesPodcastResource;
use App\Http\Resources\SongResource;
use App\Http\Resources\PodcastResource;
use App\Http\Resources\Song\OnlyBasic;
use App\Http\Resources\Song\OnlyBasicToPlay;
use App\Http\Resources\Spotify\AlbumResource as SpotifyAlbumResource;
use App\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Stevebauman\Location\Facades\Location;

trait SectionContentTrait
{

    /**
     * Get the most liked songs.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionMostLikedSongs($nb_items, $origin = "*")
    {
        $likes = DB::table('likes')
                ->select(DB::raw('COUNT(*) as likes, content_id as id'))
                ->when($origin !== "*", function ($query) use ($origin) {
                    $query->where("content_source", $origin);
                })
                ->where('content_type', 'song')
                ->groupBy('content_id')
                ->orderBy('likes', 'desc')
                ->take($nb_items)
                ->get();

        return $this->getSongs($likes);
    }
    /**
     * Get the most liked songs.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionMostLikedAlbums($nb_items, $origin = "*")
    {
        $likes = DB::table('likes')
                ->select(DB::raw('COUNT(*) as likes, content_id as id'))
                ->when($origin !== "*", function ($query) use ($origin) {
                    $query->where("content_source", $origin);
                })
                ->where('content_type', 'album')
                ->groupBy('content_id')
                ->orderBy('likes', 'desc')
                ->take($nb_items)
                ->get();

        return $this->getAlbums($likes);
    }

    /**
     * Get the new released songs.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionNewReleases($nb_items, $source = "*")
    {
        $collection = new Collection();

        if( $source === 'local' || $source === '*')
        {
            $latest_local_albums = \App\Album::orderBy('release_date', 'desc')->take($nb_items)->get();
            $local_albums = AlbumResource::collection($latest_local_albums);
            if( $nb_items >  count($latest_local_albums)) {
                $latest_local_songs = \App\Song::orderBy('created_at', 'desc')->take($nb_items - count($latest_local_albums))
                ->where('country',Location::get(Request()->ip())->countryName ?? 'Azerbaijan')
                ->where('status',0)->get();
                $local_songs =  OnlyBasicToPlay::collection($latest_local_songs);
                $collection = $collection->toBase()->merge($local_songs);
            }
            $collection = $collection->toBase()->merge($local_albums);
        }

        if( ($source === 'spotify' || $source === '*') && Setting::get('provider_spotify') )
        {
            $spotify_new_releases = Cache::remember('spotify-new-releases' . $nb_items , 240000, function() use ($nb_items) {
                return Spotify::newReleases()->limit($nb_items)->get();
            });
            $spotify_new_releases =  SpotifyAlbumResource::collection(collect(((object)$spotify_new_releases['albums']['items'])));
            $collection = $collection->toBase()->merge($spotify_new_releases);
        }

       return $collection->take($nb_items);
    }

    /**
     * Get the most popular songs ( most played ).
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionPopularSongs($nb_items, $origin = "*")
    {
        $plays = DB::table('plays')
        ->select(DB::raw('COUNT(*) as plays, content_id as id'))
        ->when($origin !== "*", function ($query) use ($origin) {
            $query->where("content_source", $origin);
        })
        ->where('content_type', 'song')
        ->groupBy('content_id')
        ->orderBy('plays', 'desc')
        ->take($nb_items)
        ->get();

        return $this->getSongs($plays);
    }

    /**
     * Get the most popular albums ( most played ).
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionPopularAlbums($nb_items, $origin = "*")
    {
        $plays = DB::table('plays')
        ->select(DB::raw('COUNT(*) as plays, content_id as id'))
        ->when($origin !== "*", function ($query) use ($origin) {
            $query->where("content_source", $origin);
        })
        ->where('content_type', 'album')
        ->groupBy('content_id')
        ->orderBy('plays', 'desc')
        ->take($nb_items)
        ->get();

        return $this->getAlbums($plays);
    }

    /**
     * Get the latest released podcasts.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionLatestPodcasts($nb_items)
    {
        return PodcastResource::collection(\App\Podcast::orderBy('created_at', 'desc')->take($nb_items)->get());;
    }

    /**
     * Get the most popular podcasts.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionPopularPodcasts($nb_items, $origin = "*")
    {
        $plays = DB::table('plays')
        ->select(DB::raw('COUNT(*) as plays, content_id as id'))
        ->when($origin !== "*", function ($query) use ($origin) {
            $query->where("content_source", $origin);
        })
        ->where('content_type', 'podcast')
        ->groupBy('content_id')
        ->orderBy('plays', 'desc')
        ->take($nb_items)
        ->get();

        return $this->getPodcasts($plays);
    }

    /**
     * Get the best podcasts (Listen Notes).
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sectionBestPodcasts($nb_items)
    {
        if( Setting::get('provider_listenNotes') ) {
            return ListenNotesPodcastResource::collection(ListenNotes::bestPodcasts())->take($nb_items);
        } else {
            return response()->json([], 500);
        }

    }
    private function getSongs($_songs)
    {
        $songs = [];
        foreach ($_songs as $song) {
            $song = Search::getSong($song->id, false);
            if( $song ) {
                array_push($songs, $song);
            }
        }
        return $songs;
    }

    private function getAlbums($_albums)
    {
        $albums = [];
        foreach ($_albums as $album) {
            $album = Search::getAlbum($album->id, false);
            if( $album ) {
                array_push($albums, $album);
            }
        }
        return $albums;
    }

    private function getPodcasts($_podcasts)
    {
        $podcasts = [];
        foreach ($_podcasts as $podcast) {
            $podcast = Search::getPodcast($podcast->id);
            if( $podcast ) {
                array_push($podcasts, $podcast);
            }

        }
        return $podcasts;
    }
}
