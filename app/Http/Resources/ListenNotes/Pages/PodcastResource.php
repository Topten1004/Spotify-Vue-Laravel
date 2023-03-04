<?php

namespace App\Http\Resources\ListenNotes\Pages;

use App\Http\Resources\ListenNotes\EpisodeResource;

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
            'origin' => 'listenNotes',
            'id' => $this->id,
            'title' => $this->title,
            'cover' => $this->image,
            'publisher' => $this->publisher,
            'last_update' => $this->latest_pub_date_ms,
            'source' => $this->listennotes_url, 
            'description' => $this->description,
            'itunes_id' => $this->itunes_id,
            'episodes' => EpisodeResource::collection(collect($this->episodes))
        ];
    }
}
