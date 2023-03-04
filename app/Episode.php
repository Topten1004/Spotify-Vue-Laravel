<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $fillable = ['title','duration','podcast_id','description', 'source', 'source_format','file_size', 'file_name'];
    
    public function podcast()
    {
        return $this->belongsTo('App\Podcast');
    }
    public function plays()
    {
        return $this->hasMany('App\Play', 'content_id')->where('plays.content_type', '=', 'episode');
    }
    public function likes() 
    {
        return $this->hasMany('App\Like', 'content_id')->where('likes.content_type', '=', 'episode');
    }
    public static function boot() {
        parent::boot();
        static::deleting(function($model) {
            // delete the episode data after deletion
            if( $model->source_format === 'file' ) {
                \App\Helpers\FileManager::delete($model->source);
            } 
            $model->likes()->delete();
            $model->plays()->delete();
        });
    }
}
