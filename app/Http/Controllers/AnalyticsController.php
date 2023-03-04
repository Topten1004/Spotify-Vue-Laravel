<?php

namespace App\Http\Controllers;


use App\Http\Resources\SongResource;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\PodcastResource;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\ArtistResource;
use App\Artist;
use App\Http\Resources\RadioStationResource;
use App\Http\Resources\Song\OnlyBasic;
use App\Http\Resources\Song\OnlyBasicToPlay;
use App\Sale;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get the songs plays based on the given duration.
     *
     * @param  $duration
     * @return \Illuminate\Http\Response
     */
    public static function getSales($duration)
    {
        if ($duration == 'lw') {
            return Sale::select(DB::raw('SUM(total_price) as total'), DB::raw('DATE(created_at) as date'))->whereDate('created_at', '>', Carbon::now()->subWeek())->groupBy('date')->orderBy('date')->get();
        } else if ($duration == 'lm') {
            return Sale::select(DB::raw('SUM(total_price) as total'), DB::raw('DATE(created_at) as date'))->whereDate('created_at', '>', Carbon::now()->subMonth())->groupBy('date')->orderBy('date')->get();
        } else {
            return Sale::select(DB::raw('SUM(total_price) as total'), DB::raw('MONTH(created_at) as date'))->whereDate('created_at', '>', Carbon::now()->subYear())->groupBy('date')->orderBy('date')->get();
        }
    }

    /**
     * Get the sales based on the given duration.
     *
     * @param  $duration
     * @return \Illuminate\Http\Response
     */
    public static function getPlays($duration)
    {
        if ($duration == 'lw') {
            return \App\Play::select(\DB::raw('COUNT(*) as plays'), \DB::raw('DATE(created_at) as date'))->whereDate('created_at', '>', Carbon::now()->subWeek())->groupBy('date')->orderBy('date')->get();
        } else if ($duration == 'lm') {
            return \App\Play::select(\DB::raw('COUNT(*) as plays'), \DB::raw('DATE(created_at) as date'))->whereDate('created_at', '>', Carbon::now()->subMonth())->groupBy('date')->orderBy('date')->get();
        } else {
            return \App\Play::select(\DB::raw('COUNT(*) as plays'), \DB::raw('MONTH(created_at) as date'))->whereDate('created_at', '>', Carbon::now()->subYear())->groupBy('date')->orderBy('date')->get();
        }
    }
    /**
     * Get the artist songs plays based on the given duration.
     *
     * @param  $duration
     * @return \Illuminate\Http\Response
     */
    public static function getArtistPlays($duration)
    {

        $artist = Artist::where('user_id', auth()->user()->id)->first();
        if ($duration == 'lw') {
            return  DB::table('plays')->select(\DB::raw('COUNT(*) as plays'), DB::raw('DATE(plays.created_at) as date'))
                ->where('plays.content_type', 'song')
                ->join('songs', 'plays.content_id', '=', 'songs.id')
                ->where('songs.artist_id', $artist->id)
                ->whereDate('plays.created_at', '>', Carbon::now()->subWeek())
                ->groupBy('date')->orderBy('date')->get();
        } else if ($duration == 'lm') {
            return  DB::table('plays')->select(\DB::raw('COUNT(*) as plays'), DB::raw('DATE(plays.created_at) as date'))
                ->where('plays.content_type', 'song')
                ->join('songs', 'plays.content_id', '=', 'songs.id')
                ->where('songs.artist_id', $artist->id)
                ->whereDate('plays.created_at', '>', Carbon::now()->subMonth())
                ->groupBy('date')->orderBy('date')->get();
        } else {
            return  DB::table('plays')->select(\DB::raw('COUNT(*) as plays'), DB::raw('DATE(plays.created_at) as date'))
                ->where('plays.content_type', 'song')
                ->join('songs', 'plays.content_id', '=', 'songs.id')
                ->where('songs.artist_id', $artist->id)
                ->whereDate('plays.created_at', '>', Carbon::now()->subYear())
                ->groupBy('date')->orderBy('date')->get();
        }
    }
    /**
     * Returns the artist analytics ( plays, populars, number of followers ).
     *
     * @return \Illuminate\Http\Response
     */
    public function artistIndex()
    {
        $artist = Artist::where('user_id', auth()->id())->first();

        $stats =  new \stdClass();
        $stats->popular_songs =  OnlyBasic::collection($artist->ownSongs()->withCount('plays')->orderBy('plays_count', 'desc')->take(5)->get());;
        $stats->plays = $this->getArtistPlays('lw');
        if( Setting::get('enable_selling') ) {
            $stats->sales = $artist->sales(5);
            $stats->nb_sales = count($artist->sales(5));
            $stats->nb_sales_this_month = count($artist->sales(5, Carbon::now()->startOfMonth()));
        }
      
        $stats->nb_followers = $artist->followers()->count();
        $stats->nb_followers_this_month = $artist->followers()->where('created_at', '>=', Carbon::now()->subMonth())->get();
        $total_plays = 0;
        foreach ($artist->ownSongs as $song) {
            $total_plays = $total_plays + $song->plays()->count();
        }
        // to add for the next update
        // foreach ($artist->podcasts->episodes as $episode) {
        //     $total_plays = $total_plays + $episode->plays()->count();
        // }
        $stats->total_plays = $total_plays;
        $stats->total_plays_this_month = array_sum($artist->ownSongs()->withCount(['plays' => function ($q) {
            $q->whereMonth('created_at', Carbon::now()->month);
        }])->get()->pluck('plays_count')->toArray());

        $stats->nb_songs = $artist->ownSongs()->count();
        $stats->nb_songs_this_month = $artist->ownSongs()->whereMonth('created_at', Carbon::now()->month)->count();
        // to add for the next update
        // $stats->nb_profile_visits = 50;
        // $stats->nb_profile_visits_this_month = 60;

        return json_encode($stats);
    }
    /**
     * Returns the analytics of the app for admin dashboard ( plays, populars ... etc).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stats =  new \stdClass();
        $stats->populars =  new \stdClass();

        $stats->plays = $this->getPlays('lw');
        if( Setting::get('enable_selling') ) {
            $stats->sales = $this->getSales('lw');
            $stats->nb_sales = \App\Sale::count();
            $stats->nb_sales_this_month = \App\Sale::whereMonth('created_at', Carbon::now()->month)->count();
        }

        $stats->populars->songs = OnlyBasicToPlay::collection(\App\Song::withCount('plays')->orderBy('plays_count', 'desc')->take(5)->get());
        $stats->populars->albums = AlbumResource::collection(\App\Album::withCount('plays')->orderBy('plays_count', 'desc')->take(5)->get());
        $stats->populars->radioStations = RadioStationResource::collection(\App\RadioStation::withCount('plays')->orderBy('plays_count', 'desc')->take(5)->get());
        $stats->populars->playlists = PlaylistResource::collection(\App\Playlist::withCount('plays')->orderBy('plays_count', 'desc')->take(5)->get());
        $stats->populars->artists = ArtistResource::collection(\App\Artist::withCount('followers')->orderBy('followers_count', 'desc')->take(5)->get());
        $stats->populars->podcasts = PodcastResource::collection(\App\Podcast::withCount('plays')->orderBy('plays_count', 'desc')->take(5)->get());

        $stats->nb_users = \App\User::count();
        $stats->nb_users_this_month = \App\User::whereMonth('created_at', Carbon::now()->month)->count();
        $stats->nb_artists = \App\Artist::count();
        $stats->nb_artists_this_month = \App\Artist::whereMonth('created_at', Carbon::now()->month)->count();
        $stats->nb_podcasts = \App\Podcast::count();
        $stats->nb_podcasts_this_month = \App\Podcast::whereMonth('created_at', Carbon::now()->month)->count();
        $stats->nb_albums = \App\Album::count();
        $stats->nb_albums_this_month = \App\Album::whereMonth('created_at', Carbon::now()->month)->count();
        $stats->nb_songs = \App\Song::count();
        $stats->nb_songs_this_month = \App\Song::whereMonth('created_at', Carbon::now()->month)->count();

        $stats->visiting_countries = \DB::table('visiting_countries')->orderBy('visitors', 'desc')->get();
        $stats->visiting_browsers = \DB::table('visiting_browsers')->orderBy('visitors', 'desc')->get();

        return json_encode($stats);
    }
}
