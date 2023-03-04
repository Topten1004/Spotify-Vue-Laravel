<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;

use Illuminate\Http\Resources\Json\JsonResource;

class PodcastResource extends JsonResource
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
            'type' => 'podcast',
            'origin' => 'local',
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'episodes' => EpisodeResource::collection($this->episodes()->orderBy('created_at','desc')->get()),
            'genres' => $this->genres,
            'nb_followers' => $this->follows->count(),
            'cover' => FileManager::asset_path($this->cover), 
            
            // links
            'spotify_link' => $this->spotify_link,    
            'youtube_link' => $this->youtube_link,    
            'soundcloud_link' => $this->soundcloud_link,    
            'itunes_link' => $this->itunes_link,    
            'deezer_link' => $this->deezer_link,    
            //
                        
            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'user' => new UserResource($this->user),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),
            $this->mergeWhen($this->artist_id, [
                'artist'=> $this->artist()
            ]),
        ];
    }
}
