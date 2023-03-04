<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
    * the attributes that are mass assignable.
    * @var array
    */ 
    protected $fillable = ['role_id','name','description'];

    protected $hidden = [
        'pivot'
    ];
    
    public function role()
    {
        return $this->belongsTo('App\Role','role_id');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_permission');
    }
    public function plans()
    {
        return $this->belongsToMany('App\Plan', 'plan_permission');
    }
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
