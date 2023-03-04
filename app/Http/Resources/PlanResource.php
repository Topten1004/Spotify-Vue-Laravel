<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => json_decode($this->active),
            'storage_space' => $this->storage_space,
            'free' => json_decode($this->free),
            'recommended' => json_decode($this->recommended), 
            'currency' => $this->currency, 
            'upgradable' => $this->upgradable, 
            'badge' => $this->badge,
            'currency_symbol' => $this->currency_symbol, 
            'interval' => $this->interval, 
            'stripe_id' => $this->stripe_id, 
            'paypal_id' => $this->paypal_id, 
            'amount' => $this->amount,  
            'position' => $this->position, 
            'displayed_features' => json_decode($this->displayed_features),
            'roles' => $this->roles,
            'permissions' => $this->permissions
        ];
    }
}
