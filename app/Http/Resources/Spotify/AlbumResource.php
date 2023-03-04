<?php

namespace App\Http\Resources\Spotify;
use Spotify;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $isExplicit = false;
    public function toArray($request)
    {
        return [
            'type' => 'album',
            'origin' => 'spotify',
            'id' => $this['id'],
            'description' => '',
            'cover' => $this['images'][1]['url'],
            'title' => $this['name'],
            'artist' => null,
            'artists' => ArtistResource::collection(collect(((object)$this['artists']))),
            'release_date' => $this['release_date'],

            // links
            'spotify_link' => $this['external_urls']['spotify'],   
            //

            'songs' => $this->getAlbumsTracks(),
            'isExplicit' => $this->isExplicit
        ];
    }

    public function getAlbumsTracks()
    {
        $songs = ((object)Spotify::albumTracks($this['id'])->get()['items']);
        foreach ($songs as $key => $value) {
           $songs->$key['cover'] =  $this['images'][1]['url'];
           if( $songs->$key['explicit'] ) {
            $this->isExplicit = true;
           }
        }
        return SongResource::collection(collect($songs));
    }
}
