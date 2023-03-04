<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;

use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
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
            'type' => "episode",
            'origin' => 'local',
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->duration,
            'source' => $this->source_format === 'file' ? FileManager::asset_path($this->source): json_decode($this->source), 
            'source_format' =>  $this->source_format, 
            'cover' => FileManager::asset_path($this->podcast->cover), 
            'podcast' => $this->podcast,
            'file_size' => $this->file_size,
            'file_name' => $this->file_name,
            'nb_likes' => $this->likes->count(),            
            'nb_plays' => $this->plays->count(),  
            'nb_downloads' => $this->download_count,          
            $this->mergeWhen( \Auth::user() && (\Auth::user()->isAdmin() || \Auth::user()->is('artist')), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
