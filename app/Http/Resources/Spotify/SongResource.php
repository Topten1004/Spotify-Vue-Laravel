<?php

namespace App\Http\Resources\Spotify;

use Illuminate\Http\Resources\Json\JsonResource;
use Youtube;
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
            'origin' => 'spotify',
            'id' => $this['id'],
            'title' => $this['name'],
            'cover' => $this->getImage(),
            'source_format' => "yt_video",
            'source' => null,
            'isExplicit' => $this['explicit'],
            'artists' => ArtistResource::collection(collect(((object)$this['artists']))),
            'artist' => null,
            'duration' => $this['duration_ms'] / 1000,
            // links
            'spotify_link' => $this['external_urls']['spotify'],
            //
            $this->mergeWhen(isSet($this['album']), [
                'album' => [
                    'id' => isSet($this['album']) ? $this['album']['id']: null,
                    'title' => isSet($this['album']) ? $this['album']['name']: null
                ],
            ])

        ];
    }

    public function getImage()
    {
        if( isset($this['cover']) )
        {
            return $this['cover'];
        }
        else if  ( isset($this['album']) )
        {
            return $this['album']['images'][1]['url'];
        }
        else if( isset($this['images']) ) {
            return $this['images'][1]['url'];
        }
        return null;
    }
}
