<?php

namespace App\Http\Resources\Artist;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\FileManager;

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
            'displayname' => $this->displayname,
            'avatar' => FileManager::asset_path($this->avatar),
            'spotify_link' => $this->spotify_link,
            'soundcload_link' => $this->soundcload_link,
            'youtube_link' => $this->youtube_link,
            'itunes_link' => $this->itunes_link,
            'deezer_link' => $this->deezer_link
        ];
    }
}
