<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StreamResource extends JsonResource
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
            'title' => $this->getTitle(),
            'artist' => $this->getArtist(),
            'genre' => $this->getGenre(),
            'status' => $this->getStatus(),
            'bitrate' => $this->getBitrate(),
            'currentListeners' => $this->getCurrentListeners(),
            'peakListeners' => $this->getPeakListeners(),
        ];
    }
    private function getTitle() 
    {
        return $this->title;
    }
    private function getArtist() 
    {
        return null;   
    }
    private function getGenre() 
    {
        return $this->genre;
    }
    private function getStatus() 
    {
        return $this->status;
    }
    private function getBitrate() 
    {
        return $this->bitrate;
    }
    private function getCurrentListeners() 
    {
        return $this->listeners;
    }
    private function getPeakListeners() 
    {
        return $this->peak;
    }
}
