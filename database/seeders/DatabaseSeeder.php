<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            AdminSeeder::class,
            CitySeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,
            TestimonialSeeder::class,
            LandingPageSectionSeeder::class,
        ]);
    }
}
