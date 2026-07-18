<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'order' => 1],
            ['name' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম', 'order' => 2],
            ['name' => 'Sylhet', 'name_bn' => 'সিলেট', 'order' => 3],
            ['name' => 'Rajshahi', 'name_bn' => 'রাজশাহী', 'order' => 4],
            ['name' => 'Khulna', 'name_bn' => 'খুলনা', 'order' => 5],
            ['name' => 'Barisal', 'name_bn' => 'বরিশাল', 'order' => 6],
            ['name' => 'Rangpur', 'name_bn' => 'রংপুর', 'order' => 7],
            ['name' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'order' => 8],
            ['name' => 'Comilla', 'name_bn' => 'কুমিল্লা', 'order' => 9],
            ['name' => 'Narayanganj', 'name_bn' => 'নারায়ণগঞ্জ', 'order' => 10],
            ['name' => 'Gazipur', 'name_bn' => 'গাজীপুর', 'order' => 11],
            ['name' => 'Jessore', 'name_bn' => 'যশোর', 'order' => 12],
            ['name' => 'Cox\'s Bazar', 'name_bn' => 'কক্সবাজার', 'order' => 13],
            ['name' => 'Bogra', 'name_bn' => 'বগুড়া', 'order' => 14],
            ['name' => 'Dinajpur', 'name_bn' => 'দিনাজপুর', 'order' => 15],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['name' => $city['name']],
                $city
            );
        }
    }
}
