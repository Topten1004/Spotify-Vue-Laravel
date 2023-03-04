<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;

use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
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
            'type' => 'album',
            'origin' => 'local',
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'songs' => SongResource::collection($this->songs()->orderBy('rank_on_album')->get()),
            'cover' => FileManager::asset_path($this->cover), 
            'release_date' => $this->release_date,
            'artists'=> $this->artists(),
            'artist' => $this->artist,
            'likes'=> $this->likes->count(),
            'plays'=> $this->plays->count(),
            // new update V2.1
            'isProduct' => $this->isProduct == "0" ? false : true,
            'isExclusive' => $this->isExclusive == "0" ? false : true,
            'isExplicit' => $this->isExplicit == "0" ? false : true,
            // 
            $this->mergeWhen($this->product,[
                'product' => new ProductResource($this->product)
            ]),
            // links
            'spotify_link' => $this->spotify_link,    
            'youtube_link' => $this->youtube_link,    
            'soundcloud_link' => $this->soundcloud_link,    
            'itunes_link' => $this->itunes_link,    
            'deezer_link' => $this->deezer_link,    
            //
            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'user' => $this->user,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
