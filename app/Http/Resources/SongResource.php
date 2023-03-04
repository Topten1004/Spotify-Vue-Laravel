<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;
use App\Http\Resources\Artist\OnlyBasic as ArtistOnlyBasic;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\User\OnlyBasic;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'type' => 'song',
            'origin' => 'local',
            'id' => $this->id,
            'title' => $this->title,
            'artists' => $this->artists(),
            'artist' => new ArtistOnlyBasic($this->artist),
            'description' => $this->description,
            'duration' => $this->duration,
            'source_format' => $this->source_format,
            'file_name' => $this->file_name,
            'source' =>  $this->source_format === 'file' ||  $this->source_format === 'audio_url' ? FileManager::asset_path($this->source) :  json_decode($this->source),
            'cover' => FileManager::asset_path($this->cover),
            'genres' => $this->genres,
            'public' => $this->public == "0"? false: true,
            // new update V2.1
            'isProduct' => $this->isProduct == "0" ? false : true,
            'isExclusive' => $this->isExclusive == "0" ? false : true,
            'isExplicit' => $this->isExplicit == "0" ? false : true,
            //
            'nb_likes' => $this->likes->count(),
            'nb_plays' => $this->plays->count(),
            'nb_downloads' => $this->download_count,
            // links
            'spotify_link' => $this->spotify_link,
            'youtube_link' => $this->youtube_link,
            'soundcloud_link' => $this->soundcloud_link,
            'itunes_link' => $this->itunes_link,
            'deezer_link' => $this->deezer_link,
            'status' => $this->status,
            //
            $this->mergeWhen($this->album_id,[
                'album' => $this->album()->select('title','id')->first()
            ]),
            $this->mergeWhen($this->product,[
                'product' => new ProductResource($this->product)
            ]),
            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'user' => new OnlyBasic($this->user),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
