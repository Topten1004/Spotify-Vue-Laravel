<?php

namespace App;

use App\Helpers\FileManager;
use App\Traits\Search;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Spotify;
class Song extends Model
{
    /**
     * the attributes that are mass assignable.
     * @var array
     */
    protected $guarded = [];

    protected $hidden = [
        'pivot'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function artists()
    {
        $artists = [];
        $pivots = \DB::table('artist_song')->where('song_id', $this->id)->get();
        foreach ($pivots as $pivot) {
            $artist = Search::getArtist($pivot->artist_id, false);
            array_push($artists, $artist);
        }
        return array_filter($artists, function($item) {
            return $item;
        });
    }
    // public function mainArtistName() {
    //     if( $this->artist ) {
    //         return $this->artist->displayname;
    //     } else if( $pivot = \DB::table('artist_song')->where('song_id', $this->id)->first()) {
    //         if ($artist = \App\Artist::find($pivot->artist_id)) {
    //             return $artist->displayname;
    //         } else if ( Setting::get('provider_spotify') ) {
    //             try {
    //                 $artist = Spotify::artist($pivot->artist_id)->get();
    //                 return $artist['name'];
    //             } catch (Exception $e) {
    //                 return '';
    //             }
    //         } else {
    //             return '';
    //         }
    //     } else {
    //         return '';
    //     }
    // }
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
    public function playlists()
    {
        return $this->belongsToMany('App\Playlist');
    }
    public function album()
    {
        return $this->belongsTo('App\Album');
    }
    public function plays()
    {
        return $this->hasMany('App\Play', 'content_id')->where('plays.content_type', '=', 'song');
    }
    public function likes()
    {
        return $this->hasMany('App\Like', 'content_id')->where('likes.content_type', '=', 'song');
    }

    public function genres()
    {
        return $this->belongsToMany('App\Genre');
    }



    public function product()
    {
        return $this->morphOne(Product::class, 'productable');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($song) {
            // delete the song data after deletion
            if( $song->source_format === 'file' && $song->source) {
                FileManager::delete($song->source);
            } 
            FileManager::delete($song->cover);
            $song->likes()->delete();
            $song->product()->delete();
            $song->plays()->delete();
        });
    }
}
