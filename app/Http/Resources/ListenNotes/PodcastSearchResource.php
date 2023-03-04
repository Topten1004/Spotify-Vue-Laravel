<?php

namespace App\Http\Resources\ListenNotes;

use App\Helpers\Content\ListenNotes\ListenNotes;
use App\Http\Resources\EpisodeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'type' => 'podcast',
            'id' => $this->id,
            'title' => $this->title_original,
            'cover' => $this->image,
            'publisher' => $this->podcast->publisher_original,
            'source' => $this->podcast->listennotes_url, 
            'description' => $this->description_original,
            'itunes_id' => $this->itunes_id,
            // 'genres' => $this->genres,
            // 'nb_followers' => $this->follows->count(),
            // 'cover' => FileManager::asset_path($this->cover), 
            // $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
            //     'user' => new UserResource($this->user),
            //     'created_at' => $this->created_at,
            //     'updated_at' => $this->updated_at,
            // ]),
            // $this->mergeWhen($this->artist_id, [
            //     'artist'=>new ArtistResource($this->artist)
        ];
    }
}
