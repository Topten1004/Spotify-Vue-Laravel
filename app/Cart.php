<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'product_id', 'price_id'];

    public function product() {
      return $this->belongsTo(Product::class);
    }

    public function price() {
      return $this->belongsTo(Price::class);
    }
}
