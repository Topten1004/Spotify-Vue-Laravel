<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable=['name','cover','icon','slug'];

    protected $hidden = [
        'pivot'
    ];
    
    public function songs()
    {
        return $this->belongsToMany('App\Song');
    }
    public static function boot() {
        parent::boot();
        // delete the genre data after deletion
        static::deleting(function($model) { 
            \App\Helpers\FileManager::delete($model->cover);
            \App\Helpers\FileManager::delete($model->icon);
        });
    }
}
