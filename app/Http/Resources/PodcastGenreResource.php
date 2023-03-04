<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;


use Illuminate\Http\Resources\Json\JsonResource;

class PodcastGenreResource extends JsonResource
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
            'slug' => $this->slug,
            'cover' => FileManager::asset_path($this->cover), 
            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}