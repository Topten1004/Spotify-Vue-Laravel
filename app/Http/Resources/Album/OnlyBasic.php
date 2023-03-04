<?php

namespace App\Http\Resources\Album;

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
            'title' => $this->title,
            'artist' => $this->artist,
            'cover' => FileManager::asset_path($this->cover)
        ];
    }
}
