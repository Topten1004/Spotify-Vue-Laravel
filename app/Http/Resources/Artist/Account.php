<?php

namespace App\Http\Resources\Artist;

use App\Http\Resources\SongResource;
use App\Http\Resources\AlbumResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\FileManager;
use App\Payout;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Account extends JsonResource
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
            'displayname' => $this->displayname,
            'firstname' => $this->firstname,
            'avatar' => FileManager::asset_path($this->avatar),
            'lastname' => $this->lastname,
            'available_disk_space' => $this->available_disk_space,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => $this->country,
            'used_disk_space' => $this->used_disk_space(),
            'spotify_link' => $this->spotify_link,
            'soundcload_link' => $this->soundcload_link,
            'youtube_link' => $this->youtube_link,
            'itunes_link' => $this->itunes_link,
            'deezer_link' => $this->deezer_link,
            'funds' => $this->funds,
            'royalties' => $this->royalties()->select(DB::RAW("COUNT('id') as total_royalties"), 'price')->groupBy('price')->get(),
            'sales' => $this->sales(),
            'payouts' => $this->payouts()->orderBy('created_at', 'desc')->get(),
            'payout_method' => $this->payout_method()->first() ?: [
                'pivot' => []
            ]
        ];
    }
}
