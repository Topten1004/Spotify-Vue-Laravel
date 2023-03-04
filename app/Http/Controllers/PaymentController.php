<?php

namespace App\Http\Controllers;

use App\Artist;
use Illuminate\Http\Request;

use App\Helpers\Billing\StripeAPI;
use App\PayoutMethod;
use App\Price;
use App\Sale;
use App\Setting;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private $stripe;


    public function __construct(){

        $this->stripe = app()->make(StripeAPI::class);
    }

    public function createIntent(Request $request)
    {
        return $this->stripe->createIntent($request);
    }

    
    private function checkout($gateway)
    {
        try {
            DB::transaction(function() use ($gateway) {
            $items = auth()->user()->carts()
            ->with('product.productable')
            ->with('price')
            ->get();

            $total_price = 0;
            foreach ($items as $item) {
                $total_price += $item->price->amount;
            }

            // creating the sale
            $sale = Sale::create([
                'user_id' => auth()->user()->id,
                'total_price' => $total_price,
                'gateway' => $gateway
            ]);

            // attaching products to the sale
            foreach ($items as $product) {
                //increase the artist funds if artist is the seller of the product
                $params = [];
                $params['price'] = $product->price->id;
                if( $artist_id = $product->product->productable->artist_id ) {
                    $artist = Artist::find( $artist_id );
                    $artist->funds +=  intval($product->price->amount * Setting::get('artist_sale_cut') / 100);
                    $artist->save();
                    $params['artist_cut'] = Setting::get('artist_sale_cut');
                }
                $sale->products()->attach($product->product_id, $params);
            }
            // empty the cart
            auth()->user()->carts()->delete();
            });
            } catch(Exception $e) {
            return response()->json(__('Transaction failed. Please try again or contact us.') ,500);
            }
    }
    
    public function checkoutWithPaypal(Request $request)
    {
        // checkout with stripe
        try {
            $this->checkout('paypal');
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(__('Transaction failed. Please try again or contact us.') ,500);
        }
    }

    public function checkoutWithStripe(Request $request)
    {
        // checkout with stripe
        try {
            $intent = $request->intent;
            $paymentMethodID = isSet($request['paymentMethod']) && $request['paymentMethod'] ? $request['paymentMethod']['id'] : null;
            $this->stripe->confirmPayment($intent, $paymentMethodID);
            // checkout with stripe
            $this->checkout('stripe');
            return response()->json([], 200);
        } catch (Exception $e) {
            throw response()->json(__('Transaction failed. Please try again or contact us.'), 500);
        }
    }

    public function prices(Request $request)
    {
        return Price::all();
    }
}
