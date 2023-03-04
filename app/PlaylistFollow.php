<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaylistFollow extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $fillable = ['user_id','playlist_id'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function playlist()
    {
        return $this->belongsTo('App\Playlist');
    }

}
