<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class NavigationItem extends Model {

    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $guarded = [];

    public function page()
    {
        return $this->belongsTo('App\Page');
    }
}
