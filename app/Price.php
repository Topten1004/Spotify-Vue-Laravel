<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['name', 'description', 'amount', 'currency', 'currency_symbol'];

    public $timestamps = false;
}
