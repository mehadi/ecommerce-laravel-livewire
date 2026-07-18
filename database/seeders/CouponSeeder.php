<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'name' => 'Welcome Discount',
            'description' => 'Get 10% off on your first order',
            'type' => 'percentage',
            'value' => 10.00,
            'minimum_amount' => 500.00,
            'usage_limit' => 100,
            'used_count' => 15,
            'starts_at' => Carbon::now()->subDays(30),
            'expires_at' => Carbon::now()->addDays(60),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SAVE50',
            'name' => 'Flat 50 Taka Off',
            'description' => 'Save 50 Taka on orders above 1000 Taka',
            'type' => 'fixed',
            'value' => 50.00,
            'minimum_amount' => 1000.00,
            'usage_limit' => 200,
            'used_count' => 42,
            'starts_at' => Carbon::now()->subDays(15),
            'expires_at' => Carbon::now()->addDays(45),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SUMMER25',
            'name' => 'Summer Sale',
            'description' => '25% off on all products during summer',
            'type' => 'percentage',
            'value' => 25.00,
            'minimum_amount' => 800.00,
            'usage_limit' => 50,
            'used_count' => 8,
            'starts_at' => Carbon::now()->subDays(10),
            'expires_at' => Carbon::now()->addDays(20),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'NEWUSER',
            'name' => 'New User Special',
            'description' => '15% discount for new customers',
            'type' => 'percentage',
            'value' => 15.00,
            'minimum_amount' => 300.00,
            'usage_limit' => null,
            'used_count' => 125,
            'starts_at' => Carbon::now()->subDays(60),
            'expires_at' => null,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'BULK100',
            'name' => 'Bulk Purchase Discount',
            'description' => 'Get 100 Taka off on orders above 2000 Taka',
            'type' => 'fixed',
            'value' => 100.00,
            'minimum_amount' => 2000.00,
            'usage_limit' => 75,
            'used_count' => 12,
            'starts_at' => Carbon::now()->subDays(5),
            'expires_at' => Carbon::now()->addDays(30),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'EXPIRED20',
            'name' => 'Expired Coupon Example',
            'description' => 'This coupon has expired',
            'type' => 'percentage',
            'value' => 20.00,
            'minimum_amount' => 400.00,
            'usage_limit' => 100,
            'used_count' => 0,
            'starts_at' => Carbon::now()->subDays(90),
            'expires_at' => Carbon::now()->subDays(30),
            'is_active' => false,
        ]);
    }
}

