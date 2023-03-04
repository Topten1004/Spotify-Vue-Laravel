<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $fillable = ['user_id','friend_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function friend()
    {
        return $this->belongsTo('App\User','friend_id');
    }
}
