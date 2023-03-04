<?php

namespace App\Http\Resources\User;

use App\Helpers\FileManager;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlyBasic extends JsonResource
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
            'email' => $this->email,
            'avatar' => FileManager::asset_path($this->avatar)
        ];
    }
}
