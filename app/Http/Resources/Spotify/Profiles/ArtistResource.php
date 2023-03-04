<?php

namespace App\Http\Resources\Spotify\Profiles;

use App\Http\Resources\Spotify\AlbumResource;
use App\Http\Resources\Spotify\SongResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Spotify;
class ArtistResource extends JsonResource
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
            'id' => $this['id'],
            'displayname' => $this['name'],
            'avatar' => isSet($this['images']) ? (isSet($this['images'][2]) ? $this['images'][2]['url'] : (isset($this['images'][0]) ? $this['images'][0]['url'] : null)) : null,
            'top_tracks' => $this->getTopTracks(),
            'albums' => $this->getAlbums(),
            'type' => 'artist',
            'origin' => 'spotify'
       ];
    }

    private function getTopTracks()
    {
        $tracks = Spotify::artistTopTracks($this['id'])->get();
        return SongResource::collection(collect((object)$tracks['tracks']));
    }

    private function getAlbums()
    {
        $albums = Spotify::artistAlbums($this['id'])->limit(10)->get();
        return AlbumResource::collection(collect((object)$albums['items']))->sortBy('release_date')->toArray();
    }
}
