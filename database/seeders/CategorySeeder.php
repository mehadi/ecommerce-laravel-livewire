<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name_en' => 'Date Molasses',
            'name_bn' => 'খেজুর গুড়',
            'slug' => 'date-molasses',
            'description_en' => 'Premium quality date molasses and related products',
            'description_bn' => 'প্রিমিয়াম মানের খেজুর গুড় এবং সম্পর্কিত পণ্য',
            'is_active' => true,
            'order' => 1,
        ]);

        Category::create([
            'name_en' => 'Natural Sweeteners',
            'name_bn' => 'প্রাকৃতিক মিষ্টি',
            'slug' => 'natural-sweeteners',
            'description_en' => 'Healthy natural sweetener alternatives',
            'description_bn' => 'স্বাস্থ্যকর প্রাকৃতিক মিষ্টি বিকল্প',
            'is_active' => true,
            'order' => 2,
        ]);

        Category::create([
            'name_en' => 'Honey Products',
            'name_bn' => 'মধু পণ্য',
            'slug' => 'honey-products',
            'description_en' => 'Pure and organic honey products',
            'description_bn' => 'খাঁটি এবং জৈব মধু পণ্য',
            'is_active' => true,
            'order' => 3,
        ]);

        Category::create([
            'name_en' => 'Organic Foods',
            'name_bn' => 'জৈব খাবার',
            'slug' => 'organic-foods',
            'description_en' => 'Certified organic food products',
            'description_bn' => 'সার্টিফাইড জৈব খাদ্য পণ্য',
            'is_active' => true,
            'order' => 4,
        ]);

        Category::create([
            'name_en' => 'Health Supplements',
            'name_bn' => 'স্বাস্থ্য সম্পূরক',
            'slug' => 'health-supplements',
            'description_en' => 'Natural health and wellness supplements',
            'description_bn' => 'প্রাকৃতিক স্বাস্থ্য এবং সুস্থতা সম্পূরক',
            'is_active' => true,
            'order' => 5,
        ]);

        Category::create([
            'name_en' => 'Spices & Herbs',
            'name_bn' => 'মসলা ও ভেষজ',
            'slug' => 'spices-herbs',
            'description_en' => 'Premium quality spices and herbs',
            'description_bn' => 'প্রিমিয়াম মানের মসলা এবং ভেষজ',
            'is_active' => true,
            'order' => 6,
        ]);
    }
}
