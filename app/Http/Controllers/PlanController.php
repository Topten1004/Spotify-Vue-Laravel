<?php

namespace App\Http\Controllers;

use App\Helpers\Billing\PayPalAPI;
use App\Helpers\Billing\StripeAPI;
use App\Http\Resources\PlanResource;
use App\Http\Resources\admin\PlanResource as AdminPlanResource;
use App\Plan;
use Illuminate\Http\Request;
use App\Setting;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        return PlanResource::collection(Plan::where('active', 1)->orderBy('position')->get());
    }

    /**
     * Display a listing of the resource for the admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AdminPlanResource::collection(Plan::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->free) {
            $request->validate([
                'name' => 'required|string',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string',
                'currency' => 'required|string',
                'amount' => 'required',
                'interval' => 'required'
            ]);
        }

        if( Setting::get('enableBilling') ) {
                    // creating the plan on Stripe if the connection is set
        if (Setting::get('stripeGateway')) {
            try {
                $stripe = new StripeAPI();
                $stripe_plan = $stripe->createPlan($request);
            } catch (\Stripe\Exception\RateLimitException $e) {
                throw new \Exception(
                    "Too many requests made to the API too quickly"
                );
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                throw new \Exception(
                    "Invalid parameters were supplied to Stripe's API"
                );
            } catch (\Stripe\Exception\AuthenticationException $e) {
                throw new \Exception(
                    "Authentication with Stripe's API failed"
                ); // (maybe you changed API keys recently)
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                throw new \Exception(
                    "Network communication with Stripe failed"
                );
            } catch (\Stripe\Exception\ApiErrorException $e) {
                throw new \Exception("Display a very generic error to the user, and maybe send");
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        if (Setting::get('paypalGateway')) {
            try {
                $paypalClient = new PayPalAPI();
                $paypal_plan = $paypalClient->createPlan($request);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        }

        $plan = Plan::create([
            'storage_space' => $request->storage_space,
            'name' =>  $request->name,
            'active' =>  $request->active,
            'amount' =>  $request->amount,
            'currency' =>  $request->currency,
            'currency_symbol' =>  $request->currency_symbol,
            'badge' =>  $request->badge,
            'interval' =>  $request->interval,
            'interval_count' =>  1,
            'recommended' =>  boolval($request->recommended),
            'free' =>  boolval($request->free),
            'upgradable' =>  boolval($request->upgradable),
            'position' =>  $request->position,
            'stripe_id' =>  isset($stripe_plan) ? $stripe_plan->id : null,
            'paypal_id' =>  isset($paypal_plan) ? $paypal_plan->id : null,
            'displayed_features' =>  $request->displayed_features,
        ]);

        foreach (json_decode($request->roles) as $role) {
            $plan->roles()->attach($role);
        }
        foreach (json_decode($request->permissions) as $permission) {
            $plan->permissions()->attach($permission);
        }
        return response()->json(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $plan = Plan::find($id);
        // update the plan on stripe & payPal gateways
        $status =  boolval($request->active);
        if (Setting::get('stripeGateway') && $plan->stripe_id && $status == !boolval($plan->active)) {
            try {
                $stripeClient = new StripeAPI();
                $stripeClient->updatePlan($plan, $status);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        if (Setting::get('paypalGateway') && $plan->paypal_id && $status == !boolval($plan->active)) {
            try {
                $paypalClient = new PayPalAPI();
                $paypalClient->updatePlan($plan, $status);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        // update the plan on the database
        $plan->storage_space = $request->storage_space;
        $plan->name =  $request->name;
        $plan->active =  $request->active;
        $plan->recommended =  boolval($request->recommended);
        $plan->upgradable =  boolval($request->upgradable);
        $plan->position =  $request->position;
        $plan->badge =  $request->badge;
        $plan->displayed_features =  $request->displayed_features;


        // datach all roles & permissions ( reset )
        $plan->detach_all_roles();
        $plan->detach_all_permissions();
        // attach new ones
        foreach (json_decode($request->roles) as $role) {
            $plan->roles()->attach($role);
        }
        foreach (json_decode($request->permissions) as $permission) {
            $plan->permissions()->attach($permission);
        }

        $plan->save();

        return response()->json(['message' => 'SUCCESS'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);
        $plan->delete();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
