<?php

use App\PayoutMethod;
use Illuminate\Database\Seeder;

class PayoutMethodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PayoutMethod::create([
            'name' => 'PayPal',
            'minimum' => 5000,
            'description' => 'Enter your PayPal address to receive your payout. Minimum payout amount is 50$.'
        ]);

        PayoutMethod::create([
            'name' => 'Perfect Money',
            'minimum' => 5000,
            'description' => 'Enter your Perfect Money address to receive your payout. Minimum payout amount is 50$.'
        ]);

        PayoutMethod::create([
            'name' => 'Bitcoin',
            'minimum' => 5000,
            'description' => 'Enter your BTC wallet to receive your payout. Minimum payout amount is equivalent to 50$.'
        ]);

        PayoutMethod::create([
            'name' => 'Bank Transfer',
            'minimum' => 10000,
            'description' => 'Enter your Bank Transfer details to receive your payout. Minimum payout amount is 100$.'
        ]);

    }
}
