<?php

namespace App\Http\Resources;

use App\Artist;
use App\Helpers\FileManager;
use App\Plan;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'lang' => $this->lang,
            'email' => $this->email,
            'avatar' => FileManager::asset_path($this->avatar),
            'badge' => $this->badge(),
            'is_admin' => $this->is_admin == "0" ? false : true,
            'plan' => $this->getPlan(),
            'used_disk_space' => $this->used_disk_space(),
            'available_disk_space' => $this->calculateDiskSpace(),
            'hide_activity' => $this->hide_activity,
            'requested_artist_account' => $this->requested_artist_account,
            'subscription' => $this->getActiveSubscription(),
            'roles' => $this->getAllRoles(),
            'permissions' => $this->getAllPermissions(),
            'card_brand' => $this->card_brand,
            'card_last_four' => $this->card_last_four,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cart' => new CartResource($this),
            $this->mergeWhen($this->artist, [
                'artist' => new ArtistResource($this->artist)
            ])
        ];
    }
    function getPlan()
    {
        if ($activeSubscription = $this->getActiveSubscription()) {
            return new PlanResource($activeSubscription->plan);
        }
        return null;
    }
    function getAllPermissions()
    {
        $user_permissions = $this->permissions()->get();
        if ($this->getPlan()) {
            $plan_permissions = $this->getPlan()->permissions()->get()->toArray();
            return  array_unique(array_merge($plan_permissions, $user_permissions->toArray()), SORT_REGULAR);
        } else {
            return  $user_permissions;
        }
    }
    function getActiveSubscription()
    {
        if($activeSubscription = $this->subscriptions()->where('status', 'active')->first()) {
            return $activeSubscription;
        }
        return null;
    }
    function getAllRoles()
    {
        $user_roles = $this->roles()->get();
        if ($this->getPlan()) {
            $plan_roles = $this->getPlan()->roles()->get()->toArray();
            return  array_unique(array_merge($plan_roles, $user_roles->toArray()), SORT_REGULAR);
        } else {
            return  $user_roles;
        }
    }
    function calculateDiskSpace()
    {
        if ($this->getPlan()) {
            return  $this->available_disk_space > $this->getPlan()->storage_space  ? $this->available_disk_space : $this->getPlan()->storage_space;
        } else {
            return  $this->available_disk_space;
        }
    }
}
