<?php

use Illuminate\Database\Seeder;

use App\Plan;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::truncate();

        Plan::create([
            'name' => 'Basic',
            'amount' => 0,
            'free' => 1,
            'position' => 1,
            'recommended' => 0,
            'interval' => 'month',
            'interval_count' => 1,
            'currency' => 'USD',
            'storage_space' => 100,
            'displayed_features' => json_encode([
                'Up to 100MB of storage' ,'Create Playlists & Share', 'Unlimited Downloads'
            ])
            // 'displayed_features' => "['Up to 100MB of storage','Create Playlists & Share', 'Unlimited Downloads']"
        ]);

    }
}
