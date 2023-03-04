<?php

namespace App\Helpers\Billing;

use App\User;
use App\Plan;
use App\Setting;
use App\Subscription;

class PayPalAPI
{

    private $access_token;
    private $client;
    private $url;

    public function __construct()
    {
        try {
            $this->establishConnection();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function establishConnection()
    {
        $isSandbox = boolval(config('services.paypal.sandbox'));
        $key = config('services.paypal.key');
        $secret = config('services.paypal.secret');
        $this->url = $isSandbox ? 'https://api-m.sandbox.paypal.com' :'https://api-m.paypal.com';
        try {
            $this->client = new \GuzzleHttp\Client();
            $response = $this->client->request(
                'POST',
                $this->url . '/v1/oauth2/token',
                [
                    'headers' => [
                        "Accept" =>  "application/json",
                        "Content-Type" =>  "application/x-www-form-urlencoded"
                    ],
                    'auth' => [
                        $key,
                        $secret,
                        'basic'
                    ],
                    'body' => 'grant_type=client_credentials'
                ]
            );
            // to change
            $a = $response->getBody()->getContents();
            $this->access_token = json_decode($a)->access_token;
        } catch(\Exception $e) {
            throw $e;
        }
    }
    public function synchnorizePlans() {
        $plans = \App\Plan::all();
        foreach ($plans as $plan) {
            if( !$plan->free ) {
                if( $plan->paypal_id ) {
                    try {
                        $this->client->request(
                            'GET',
                            $this->url . '/v1/billing/plans/' . $plan->paypal_id,
                            [
                                'headers' => [
                                    "Content-Type" =>  "application/json",
                                    "Authorization" => 'Bearer ' .  $this->access_token
                                ]
                            ]
                        ); 
                    } catch(\Exception $e) {
                        try {
                            $paypalPlan = $this->createPlan($plan);
                            $plan->paypal_id = $paypalPlan->id;
                            $plan->save();
                        } catch(\Exception $e) {
                            throw new \Exception("Failed to synchnorize plans on paypal");
                        }
                    }
    
                } else {
                    try {
                        $paypalPlan = $this->createPlan($plan);
                        $plan->paypal_id = $paypalPlan->id;
                        $plan->save();
                    } catch(\Exception $e) {
                        throw $e;
                    }
                }
            }

        }
        return "Plans synchnorized successfully";
    }
    /**
     * Creates a product on the stripe gateway
     */
    public function createProduct($name)
    {

        $response = $this->client->request(
            'POST',
            $this->url . '/v1/catalogs/products',
            [
                'headers' => [
                    "Content-type" =>  "application/json",
                    "Authorization" => 'Bearer ' .  $this->access_token
                ],
                'json' => [
                    'name' => $name
                ]
            ]
        );

        $response_array = json_decode($response->getBody()->getContents());
        $product = $response_array;
        return $product;
    }
    /**
     * Create plan
     */
    public function createPlan($data)
    {
        $product = $this->createProduct($data['name']);
        $plan = $this->client->request(
            'POST',
            $this->url . '/v1/billing/plans',
            [
                'headers' => [
                    "Content-Type" =>  "application/json",
                    "Authorization" => 'Bearer ' .  $this->access_token
                ],
                'json' => array(
                    'name' => $data['name'],
                    'product_id' => $product->id,
                    'status' => 'ACTIVE',
                    'billing_cycles' =>
                    array(
                        0 =>
                        array(
                            'frequency' =>
                            array(
                                'interval_unit' => $data['interval'],
                                'interval_count' => 1,
                            ),
                            'tenure_type' => 'REGULAR',
                            'sequence' => 1,
                            'total_cycles' => 0,
                            'pricing_scheme' =>
                            array(
                                'fixed_price' =>
                                array(
                                    'value' => strval(round($data['amount'] / 100, 2)),
                                    'currency_code' => $data['currency'],
                                ),
                            ),
                        ),
                    ),
                    'payment_preferences' =>
                    array(
                        'auto_bill_outstanding' => true,
                        'setup_fee' =>
                        array(
                            'value' => strval(round($data['amount'] / 100, 2)),
                            'currency_code' => $data['currency'],
                        ),
                        'setup_fee_failure_action' => 'CANCEL',
                        'payment_failure_threshold' => 3,
                    ),
                )
            ]
        );
        $plan = json_decode($plan->getBody()->getContents());
        return $plan;
    }
    /**
     * Update plan
    */
    public function updatePlan($plan, $status)
    {
        // updates status only
        $uri = $this->url . '/v1/billing/plans/' . $plan->paypal_id . '/' . ($status? 'activate' : 'deactivate');
        $this->client->request(
            'POST',
            $uri,
            [
                'headers' => [
                    "Content-Type" =>  "application/json",
                    "Authorization" => 'Bearer ' .  $this->access_token
                ]
            ]
        );
    }
    /**
     * Delete subscription
    */
    public function cancelSubscription($subscription) {
        $uri = $this->url . '/v1/billing/subscriptions/'. $subscription->gateway_id .'/cancel';
        $this->client->request(
            'POST',
            $uri,
            [
                'headers' => [
                    "Content-Type" =>  "application/json",
                    "Authorization" => 'Bearer ' .  $this->access_token
                ],
                'json' => [
                    'reason' => 'reason'
                ]
            ]
        );
    }
}
