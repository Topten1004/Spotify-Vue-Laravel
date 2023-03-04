<?php

namespace App;

use App\Helpers\FileManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
class Artist extends Model
{
    use Notifiable;
    /**
    * the attributes that are mass assignable.
    * @var array
    */
    // protected $fillable = ['user_id','avatar','firstname','lastname','displayname'];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function featuredAlbums()
    {
        return $this->belongsToMany(Album::class, 'album_artist', 'artist_id', 'album_id');
    }



    public function ownAlbums()
    {
        return $this->hasMany(Album::class);
    }
    public function podcasts()
    {
        return $this->hasMany('App\Podcast');
    }
    public function followers()
    {
        return $this->hasMany('App\Follow', 'followed_id')->where('follows.followed_type', '=', 'artist');
    }
    public function ownSongs()
    {
        return $this->hasMany(Song::class);
    }

    public function ownSongsOnlyActive()
    {
        return $this->hasMany(Song::class)->where('status',0);
    }


    public function featuredSongs()
    {
        return $this->belongsToMany(Song::class);
    }

    public function sales($rows = 200, $from_date = null)
    {
        $song_sales = DB::table('sales')
        ->select(
            DB::raw('sale_product.artist_cut as artist_cut'),
            DB::raw('songs.title as itemTitle'),
            DB::raw('songs.cover as cover'),
            DB::raw('prices.amount as amount'),
            DB::raw('prices.currency_symbol as priceSymbol'),
            DB::raw('prices.name as priceName'),
            DB::raw('songs.artist_id as artist_id')
        )
        ->join( 'sale_product', 'sales.id', '=', 'sale_product.sale_id' )
        ->join( 'products', 'products.id', '=', 'sale_product.product_id' )
        ->join( 'songs', 'songs.id', '=', 'products.productable_id' )
        ->join( 'prices', 'prices.id', '=', 'sale_product.price' )
        ->where('songs.artist_id', $this->id)
        ->orderBy('sales.created_at', 'desc')
        ->when($from_date, function ($query, $from_date) {
            return $query->where('sales.created_at', '>', $from_date);
        })
        ->take($rows)
        ->get()->toArray();

        $album_sales = DB::table('sales')
        ->select(
            DB::raw('albums.title as itemTitle'),
            DB::raw('sale_product.artist_cut as artist_cut'),
            DB::raw('albums.cover as cover'),
            DB::raw('prices.amount as amount'),
            DB::raw('prices.currency_symbol as priceSymbol'),
            DB::raw('prices.name as priceName'),
            DB::raw('albums.artist_id as artist_id')
        )
        ->join( 'sale_product', 'sales.id', '=', 'sale_product.sale_id' )
        ->join( 'products', 'products.id', '=', 'sale_product.product_id' )
        ->join( 'albums', 'albums.id', '=', 'products.productable_id' )
        ->join( 'prices', 'prices.id', '=', 'sale_product.price' )
        ->where('albums.artist_id', $this->id)
        ->orderBy('sales.created_at', 'desc')
        ->when($from_date, function ($query, $from_date) {
            return $query->where('sales.created_at', '>', $from_date);
        })
        ->take($rows)
        ->get()->toArray();

        $sales = array_merge($song_sales, $album_sales);

        foreach ($sales as $sale) {
            $sale->cover = FileManager::asset_path($sale->cover);
        }

        return $sales;
    }

    public function royalties()
    {
        return $this->hasMany(Royalty::class);
    }

    public function payout_method() {
        return $this->belongsToMany(PayoutMethod::class, 'payout_method_artist', 'artist_id', 'payout_method_id')->withPivot('payout_details');
    }

    public function payouts() {
        return $this->hasMany(Payout::class);
    }

    public function used_disk_space() {
        return $this->ownSongs()->where('uploaded_by','=','artist')->sum('file_size');
    }
    public static function boot() {
        parent::boot();
        static::deleting(function($model) {
            // delete the arist data after deletion
            \App\Helpers\FileManager::delete($model->avatar);
            $model->ownAlbums()->delete();
            $model->podcasts()->delete();
        });
    }

    public function scopeIsActive($query){
        return $query->where('status',0);
    }
}
