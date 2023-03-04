<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
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
            'type' => 'playlist',
            'id' => $this->id,
            'title' => $this->title,
            'public' => $this->public == "0" ? false: true,
            'cover' => FileManager::asset_path($this->cover), 
            'songs' => $this->songs(),
            'created_by' => $this->created_by,
            'nb_followers' => $this->followers->count(),
            'user' => $this->user()->select('name', 'id')->first(),
            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
