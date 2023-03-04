<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $guarded = ['isEditable'];

    public function sections()
    {
        return $this->hasMany('App\Section');
    }
}
