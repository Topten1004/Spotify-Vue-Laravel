<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PodcastFollow extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $fillable = ['user_id','podcast_id'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function podcast()
    {
        return $this->belongsTo('App\Podcast');
    }
}
