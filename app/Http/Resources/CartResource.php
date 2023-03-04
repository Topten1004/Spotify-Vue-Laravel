<?php

namespace App\Http\Resources;

use App\Helpers\FileManager;
use App\Http\Resources\Album\OnlyBasic as AlbumOnlyBasic;
use App\Http\Resources\Song\OnlyBasic as SongOnlyBasic;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class CartResource extends JsonResource
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
            'items' => $this->items(),
            'total' => $this->total()
        ];
    }

    public function items() {
        $carts = $this->carts()
        ->with('product.productable')
        ->with('price')
        ->get();

        $items = [];
        foreach ($carts as $item) {
            if( $item->product->productable_type == 'App\Song') {
                $item->item = new SongOnlyBasic($item->product->productable);
            } else if ( $item->product->productable_type == 'App\Album' ) {
                $item->item  = new AlbumOnlyBasic($item->product->productable);
            }
            array_push($items, $item);
        }

        return $items;
    
    }

    public function total() {
        $total = 0;
        foreach ($this->items() as $item) {
            $total += $item->price->amount;
        }
        return $total;
    }
}
