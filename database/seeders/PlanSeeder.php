<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 0,
                'max_products' => 100,
                'max_admin_users' => 2,
                'max_custom_domains' => 0,
                'features' => [],
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'price' => 29,
                'max_products' => 500,
                'max_admin_users' => 10,
                'max_custom_domains' => 1,
                'features' => ['coupons_enabled'],
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 99,
                'max_products' => null,
                'max_admin_users' => null,
                'max_custom_domains' => 5,
                'features' => ['coupons_enabled', 'landing_pages_enabled', 'advanced_analytics_enabled'],
                'is_default' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
