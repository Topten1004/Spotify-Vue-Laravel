<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
    */

    public function push(Request $request)
    {
        // check if the product already exists on the cart
        foreach (auth()->user()->carts as $cart) {
            if( $cart->product->id == $request->product['id'] ) {
                return response()->json( __('Oops, item already exists on your cart.') ,500);
            }   
        }
        // pushing to cart
        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product['id'],
            'price_id' => $request->price['id']
        ]);

        return new CartResource(auth()->user());
    }
    /**
     * Update the specified resource in storage.
     *
    */

    public function remove(Request $request)
    {
        $product_id = $request->id;

        auth()->user()->carts()->where('product_id', $product_id)->delete();
        
        return new CartResource(auth()->user());
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
