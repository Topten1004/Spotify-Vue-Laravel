<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PodcastGenre extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $guarded = [];

    public function podcasts()
    {
        return $this->belongsToMany('App\Podcast','genre_podcast','genre_id','podcast_id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($model) {
            // delete the podcast genre data after deletion
            \App\Helpers\FileManager::delete($model->cover);
        });
    }
}
