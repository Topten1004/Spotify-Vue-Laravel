<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['user_id', 'total_price'];

    public function products() {
        return $this->belongsToMany(Product::class, 'sale_product', 'sale_id', 'product_id')->withPivot('price', 'artist_cut');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
