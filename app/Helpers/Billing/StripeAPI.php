<?php 

namespace App\Helpers\Billing;
use \Stripe\Exception\InvalidRequestException as InvalidRequestException;
use App\User;
use App\Plan;
use App\Setting;
use App\Subscription;

class StripeAPI {

    private $stripe;
    
    public function __construct(){
        if( config('services.stripe.key') && Setting::get('enableBilling')) {
            try {
                $this->establishConnection();
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
    public function establishConnection() {
        $this->stripe = new \Stripe\StripeClient(
            config('services.stripe.secret')
        );
        $this->stripe->products->all();
    }
    public function synchnorizePlans() {
        $plans = \App\Plan::all();
        foreach ($plans as $plan) {
            if( !$plan->free ) {
                if( !$plan->stripe_id || !$this->stripe->plans->retrieve($plan->stripe_id)) {
                    try {
                        $stripePlan = $this->createPlan($plan);
                        $plan->stripe_id = $stripePlan->id;
                        $plan->save();
                    } catch (\Exception $e) {
                        throw new \Exception("Failed to synchnorize plans on stripe");
                    }
                }
            }
        }
        return "Plans synchnorized successfully";
    }
    public function createNewCustomer(User $user, $payment_method) {
        if( $payment_method ) {
            $stripe_payment_method = $this->stripe->paymentMethods->retrieve(
                $payment_method,
                []
            );
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'payment_method' =>  $stripe_payment_method->id,
                'invoice_settings' => [
                    'default_payment_method' =>  $stripe_payment_method->id
                ],
              ]);
            $user->card_brand = $stripe_payment_method->card->brand;
            $user->card_last_four = $stripe_payment_method->card->last4;
            $user->stripe_id = $customer->id;
            $user->save();
        } else {
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name
              ]);
        }
        return $customer;
    }
    /**
     * Create plan on the stripe gateway
     */
    public function createPlan($plan) {
 
        $prod = $this->stripe->products->create([
            'name' =>  $plan->name
        ]);
        return $this->stripe->plans->create([
            'product' =>  $prod->id,
            'amount' =>  $plan->amount,
            'currency' =>  $plan->currency,
            'interval' =>  $plan->interval
        ]);
    }
    /**
     * Update plan on the stripe gateway
     */
    public function updatePlan($plan, $status) {
 
        return $this->stripe->plans->update($plan->stripe_id, [
            'active' =>  $status
        ]);
    }
    public function updatePaymentMethod( $customer, $user, $payment_method ) {
        $stripe_payment_method = $this->stripe->paymentMethods->retrieve(
            $payment_method,
            []
        );
            $this->stripe->customers->update($customer->id, [
            'payment_method' =>  $stripe_payment_method->id,
            'invoice_settings' => [
                'default_payment_method' =>  $stripe_payment_method->id
            ],
          ]);
          $user->card_brand = $stripe_payment_method->card->brand;
          $user->card_last_four = $stripe_payment_method->card->last4;
          $user->save();
    }
    public function createOrFindCustomer(User $user, $payment_method) {
        if ($user->stripe_id) {
            try {
                $customer = $this->stripe->customers->retrieve($user->stripe_id);
                if( $payment_method ) {
                    $this->updatePaymentMethod($customer, $user, $payment_method);
                }
                return $customer;
            } catch(InvalidRequestException $e) {}
            catch(\Exception $e) {
                throw $e;
            }
        } else {
            // Here we will create the customer instance on Stripe and store the ID of the
            // user from Stripe. user ID will correspond with the Stripe user instances
            // and allow us to retrieve users from Stripe later when we need to work.
            $customer = $this->createNewCustomer($user, $payment_method);
        }
        return $customer;
    }

    public function createSubscription(User $user, Plan $plan, $payment_method) {
      
        $customer = $this->createOrFindCustomer($user, $payment_method);
  
        $stripe_subscription = $this->stripe->subscriptions->create([
            'customer' =>  $customer->id,
            'items' =>  [
                ['price' => $plan->stripe_id]
            ]
        ]);

        return $stripe_subscription;
    }

    public function cancelSubscription(Subscription $subscription) {
        return $this->stripe->subscriptions->cancel(
                $subscription->gateway_id,
            []
          );
    }

    public function createIntent($details)
    {
        $create = [
            'amount' => $details->amount,
            'currency' => json_decode(Setting::get('default_currency'))->value,
            // Verify your integration in this guide by including this parameter
            'setup_future_usage' => 'off_session',
            'metadata' => ['integration_check' => 'accept_a_payment'],
        ];
        $intent = $this->stripe->paymentIntents->create($create);
 
        return $intent;
    }

    public function confirmPayment($intent, $paymentMethodID)
    {
        // getting the customer
        // $a = auth()->user()->stripe_id;
        $customer = $this->createOrFindCustomer(auth()->user(), $paymentMethodID);
        // dd( $a, $customer->id );
        $this->stripe->paymentIntents->update($intent, [
            'customer' => $customer->id
        ]);
        $confirmation = $this->stripe->paymentIntents->confirm(
            $intent,
            ['payment_method' =>  $customer->invoice_settings['default_payment_method']]
        );

        return $confirmation;
    }

    public function createPaymentMethod($details)
    {

        // $payment = $this->stripe->paymentMethods->create([
        //     'type' => 'card',
        //     'card' => [
        //       'number' => $details,
        //       'exp_month' => 6,
        //       'exp_year' => 2022,
        //       'cvc' => '314',
        //     ],
        //   ]);
          
        // $intent = $this->stripe->paymentIntents->confirm([
        //     $details->intent,
        //     [
        //         'payment_method'
        //     ]
        // ]);

        // return $intent;
    }
}