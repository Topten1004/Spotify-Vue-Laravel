<?php

namespace App\Http\Controllers;

use App\Helpers\Billing\PayPalAPI;
use App\Helpers\Billing\StripeAPI;
use App\Http\Resources\SubscriptionResource;
use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubscriptionResource::collection(Subscription::orderBy('created_at','desc')->get());
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'gateway' => 'required'
        ]);
        
        Subscription::create([
            'status' => 'active',
            'plan_id' => $request->plan_id,
            'user_id' => $request->user_id,
            'gateway' => $request->gateway,
            // 'renews_at' => $request->renews_at 
        ]);

        return response()->json(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = Subscription::find($id);
        if( $subscription->gateway_id ) {
            if( $subscription->gateway === 'stripe' ) {
                $stripe = new StripeAPI();
                try {
                    $stripe->cancelSubscription($subscription);
                } catch (\Exception $e) {
                    throw new FEException('Failed to delete subscription from Stripe.', '', 500);
                }
            }
            if( $subscription->gateway === 'paypal' ) {
                $paypal = new PayPalAPI();
                try {
                    $paypal->cancelSubscription($subscription);
                } catch (\Exception $e) {
                    throw new FEException('Failed to delete subscription from PayPal.', '', 500);
                }
            }
        }
        $subscription->status = 'canceled';
        $subscription->save();

        return response()->json(null, 200);
    }
}
