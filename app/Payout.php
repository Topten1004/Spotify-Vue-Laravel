<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $guarded = [];

    public function artist() {
        return $this->belongsTo(Artist::class);
    }
}
