<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'active',
        'displayed_features',
        'currency',
        'currency_symbol',
        'interval',
        'interval_count',
        'amount',
        'free',
        'recommended',
        'upgradable',
        'badge',
        'stripe_id',
        'paypal_id',
        'position',
        'storage_space'
    ];
    public function permissions()
    {
        return $this->belongsToMany('App\Permission', 'plan_permission');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function subscriptions()
    {
        return $this->hasMany('App\Subscritpion');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'plan_role');
    }
    public function detach_all_permissions() {
        foreach( $this->permissions as $permission ) {
            $this->permissions()->detach($permission->id);
        }
    } 
    public function detach_all_roles() {
        foreach( $this->roles as $role ) {
            $this->roles()->detach($role->id);
        }
    } 
}
