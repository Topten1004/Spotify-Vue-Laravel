<?php

namespace App\Http\Resources;
use App\Helpers\FileManager;
use App\Http\Resources\User\OnlyBasic;
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
            'id' => $this->id,
            'origin' => 'local',
            'displayname' => $this->displayname,
            'avatar' => FileManager::asset_path($this->avatar),
            'available_disk_space' => $this->available_disk_space,
            'used_disk_space' => $this->used_disk_space(),
            'nb_followers' => $this->followers()->count(),
            'nb_albums' => $this->featuredAlbums->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => $this->country,
            'type' => 'artist',

            // links
            'spotify_link' => $this->spotify_link,    
            'youtube_link' => $this->youtube_link,    
            'soundcloud_link' => $this->soundcloud_link,    
            'itunes_link' => $this->itunes_link,    
            'deezer_link' => $this->deezer_link,    
            //

            $this->mergeWhen(\Auth::user() && \Auth::user()->isAdmin(), [
                'user' => new OnlyBasic($this->user),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
