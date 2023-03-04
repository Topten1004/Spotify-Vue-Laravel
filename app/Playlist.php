<?php

namespace App;

use App\Http\Resources\SongResource;
use App\Http\Resources\Spotify\SongResource as SpotifySongResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spotify;
use Stevebauman\Location\Facades\Location;

class Playlist extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function songs()
    {
        // return $this->belongsToMany('App\Song');
        $relations = DB::table("playlist_song")->where("playlist_id", $this->id)->get();

        $songs = [];

        foreach ($relations as $relation) {
            if( $relation->song_origin === "spotify" )
            {
                $song = Spotify::track($relation->song_id)->get();
                $song = new SpotifySongResource($song);
            } else if ( $relation->song_origin === "local" ) {
                $song = Song::where('status',0)
                ->where('country',Location::get(Request()->ip())->countryName ?? 'Azerbaijan')
                ->find($relation->song_id);
                $song = new SongResource($song);
            }
            if( isset($song) ) {
                array_push($songs, $song);
            }
        }

        return $songs;
    }

    public function followers()
    {
        return $this->hasMany('App\Follow', 'followed_id')->where('follows.followed_type', '=', 'playlist');
    }
    public function plays()
    {
        return $this->hasMany('App\Play', 'content_id')->where('plays.content_type', '=', 'playlist');
    }
    public static function boot() {
        parent::boot();
        static::deleting(function($model) {
            // delete the playlist data after deletion
            \App\Helpers\FileManager::delete($model->cover);
        });
    }
}
