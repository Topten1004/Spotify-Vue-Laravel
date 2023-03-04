<?php

namespace App\Http\Resources\ListenNotes;

use App\Helpers\FileManager;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
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
            'type' => "episode",
            'origin' => "listenNotes",
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->audio_length_sec,
            'source' => $this->audio, 
            'cover' => $this->image, 
            'source_format' =>  'file', 
            'created_at' => Carbon::createFromTimestampMs($this->pub_date_ms)->format('Y-m-d')       
            // $this->mergeWhen( \Auth::user() && (\Auth::user()->isAdmin() || \Auth::user()->is('artist')), [
            //     'created_at' => $this->created_at,
            //     'updated_at' => $this->updated_at,
            // ])
        ];
    }
}
