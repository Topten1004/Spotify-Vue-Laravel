<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['productable_id', 'productable_type'];

    public $timestamps = false;

    public function productable()
    {
        return $this->morphTo();
    }

    public function prices()
    {
        return $this->belongsToMany(Price::class, 'product_price', 'product_id', 'price_id');
    }

}
