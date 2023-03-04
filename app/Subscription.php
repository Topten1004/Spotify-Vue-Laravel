<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'plan_id', 'status', 'renews_at', 'gateway', 'gateway_id'];
        /**
     * Get the model related to the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function plan()
    {
        return $this->belongsTo('App\Plan');
    }
}
