<?php

namespace App\Http\Resources\Song;

use App\Helpers\FileManager;
use App\Http\Resources\Artist\OnlyBasic;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlyBasicToPlay extends JsonResource
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
            'artist' => new OnlyBasic($this->artist),
            'duration' => $this->duration,
            'source_format' => $this->source_format,
            'file_name' => $this->file_name,
            'source' =>  $this->source_format === 'file' ||  $this->source_format === 'audio_url' ? FileManager::asset_path($this->source) :  json_decode($this->source),
            'cover' => FileManager::asset_path($this->cover),
            // new update V2.1
            'isProduct' => $this->isProduct == "0" ? false : true,
            'isExclusive' => $this->isExclusive == "0" ? false : true,
            'isExplicit' => $this->isExplicit == "0" ? false : true,
            //              
            //
            $this->mergeWhen($this->album_id,[
                'album' => $this->album()->select('title','id')->first()
            ]),
            $this->mergeWhen($this->product,[
                'product' => new ProductResource($this->product)
            ])
        ];
    }
}
