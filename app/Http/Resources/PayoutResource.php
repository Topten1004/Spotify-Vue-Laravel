<?php

namespace App\Http\Resources;

use App\Http\Resources\Artist\Account;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutResource extends JsonResource
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
            'amount' => $this->amount,
            'artist' => new Account($this->artist),
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}
