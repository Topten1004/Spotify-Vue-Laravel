<?php

namespace App\Http\Resources\Spotify;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'origin' => 'spotify',
            'type' => 'artist',
            'displayname' => $this['name'],
            'type' => 'artist',
            'avatar' => isset($this['images']) ? (isset($this['images'][2]) ? $this['images'][2]['url'] : (isset($this['images'][0]) ? $this['images'][0]['url'] : null)) : null,

            // links
            'spotify_link' => $this['external_urls']['spotify'] 
            //

        ];
    }
}
