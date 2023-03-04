<?php

namespace App\Http\Resources\profiles;
use App\Helpers\FileManager;
use App\Http\Resources\SongResource;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\ArtistResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        'name' => $this->name,
        'email' => $this->email,
        'plan' => $this->plan(),
        'avatar' =>  FileManager::asset_path($this->avatar),
        'likes'  => $this->likes('song'),
        'mostPlayed'  => SongResource::collection(\App\Song::whereIn('id',$this->plays()->selectRaw('content_id, COUNT(*)')->groupBy('content_id')->get()->pluck('content_id'))->get()),
        'playlists'  =>  PlaylistResource::collection($this->playlists()->where('created_by','!=','admin')->where('public',1)->get()),
        // 'followed_playlists'  =>  $this->followed_playlists(),
        // 'followed_artists'  =>  ArtistResource::collection($this->followed_artists()->with('artist')->get()->pluck('artist')),
        'nb_friends'  => $this->friends()->count()
        ];
    }
}
