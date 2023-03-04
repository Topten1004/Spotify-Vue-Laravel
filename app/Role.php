<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $hidden = [
        'pivot'
    ];

    public function user()
    {
        return $this->belongsToMany('App\User');
    }

    public function current_permissions()
    {
        return $this->belongsToMany('App\Permission', 'role_permission');
    }

    public function available_permissions()
    {
        return $this->hasMany('App\Permission');
    }
    public function plans()
    {
        return $this->belongsToMany('App\Plan', 'plan_role');
    }
    // detach all permissions from the role
    public function detach_all() {
        foreach( $this->available_permissions as $permission ) {
            $this->current_permissions()->detach($permission->id);
        }
    } 
    
    // reset the default permissions of the role
    public function add_default_permissions () {
        foreach( $this->available_permissions  as $permission )  {
            $this->current_permissions()->attach($permission->id);
        }
    }
}
