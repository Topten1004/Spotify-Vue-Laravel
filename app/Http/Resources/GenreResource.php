<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;

use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
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
            'type' => 'genre',
            'origin' => 'local',
            'id' => $this->id,
            'name' => $this->name,
            'cover' => FileManager::asset_path($this->cover), 
            'icon' => FileManager::asset_path($this->icon), 
            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
