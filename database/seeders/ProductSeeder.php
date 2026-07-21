<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create if they don't exist to prevent duplicate key errors
        if (Product::count() === 0) {
            $category = Category::where('slug', 'date-molasses')->first();

            Product::create([
                'category_id' => $category?->id,
                'name_en' => 'Premium Date Molasses',
                'name_bn' => 'প্রিমিয়াম খেজুর গুড়',
                'description_en' => '100% pure, organic date molasses extracted from the finest dates. Rich in minerals, vitamins, and natural sweetness. Perfect for health-conscious individuals looking for a natural sugar alternative.',
                'description_bn' => 'সর্বোত্তম খেজুর থেকে নিষ্কাশিত ১০০% খাঁটি, জৈব খেজুর গুড়। খনিজ, ভিটামিন এবং প্রাকৃতিক মিষ্টিতে সমৃদ্ধ। প্রাকৃতিক চিনির বিকল্প খুঁজছেন এমন স্বাস্থ্য সচেতন ব্যক্তিদের জন্য উপযুক্ত।',
                'price' => 350.00,
                'compare_at_price' => 450.00,
                'buying_price' => 195.00,
                'sku' => 'DTM-001',
                'stock' => 100,
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ]);

            Product::create([
                'category_id' => $category?->id,
                'name_en' => 'Organic Date Syrup',
                'name_bn' => 'জৈব খেজুর সিরাপ',
                'description_en' => 'Pure organic date syrup with a smooth, rich flavor. Ideal for baking, cooking, and as a natural sweetener for beverages.',
                'description_bn' => 'সmooth, সমৃদ্ধ স্বাদের সঙ্গে খাঁটি জৈব খেজুর সিরাপ। বেকিং, রান্না এবং পানীয়ের জন্য প্রাকৃতিক মিষ্টি হিসেবে আদর্শ।',
                'price' => 280.00,
                'compare_at_price' => 350.00,
                'buying_price' => 158.00,
                'sku' => 'DTS-002',
                'stock' => 75,
                'is_active' => true,
                'is_featured' => false,
                'order' => 2,
            ]);

            // Natural Sweeteners Category
            $naturalSweeteners = Category::where('slug', 'natural-sweeteners')->first();

            Product::create([
                'category_id' => $naturalSweeteners?->id,
                'name_en' => 'Raw Organic Honey',
                'name_bn' => 'কাঁচা জৈব মধু',
                'description_en' => 'Pure, unfiltered raw honey collected from wildflowers. Rich in enzymes and natural antioxidants.',
                'description_bn' => 'বন্য ফুল থেকে সংগ্রহ করা খাঁটি, অপরিশোধিত কাঁচা মধু। এনজাইম এবং প্রাকৃতিক অ্যান্টিঅক্সিডেন্টে সমৃদ্ধ।',
                'price' => 450.00,
                'compare_at_price' => 550.00,
                'buying_price' => 258.00,
                'sku' => 'HNY-001',
                'stock' => 50,
                'is_active' => true,
                'is_featured' => true,
                'order' => 3,
            ]);

            Product::create([
                'category_id' => $naturalSweeteners?->id,
                'name_en' => 'Coconut Sugar',
                'name_bn' => 'নারকেল চিনি',
                'description_en' => 'Natural coconut palm sugar with low glycemic index. Perfect sugar alternative for diabetics.',
                'description_bn' => 'নিম্ন গ্লাইসেমিক সূচক সহ প্রাকৃতিক নারকেল পাম চিনি। ডায়াবেটিস রোগীদের জন্য নিখুঁত চিনির বিকল্প।',
                'price' => 320.00,
                'compare_at_price' => 400.00,
                'buying_price' => 178.00,
                'sku' => 'CSU-001',
                'stock' => 60,
                'is_active' => true,
                'is_featured' => false,
                'order' => 4,
            ]);

            // Honey Products Category
            $honeyProducts = Category::where('slug', 'honey-products')->first();

            Product::create([
                'category_id' => $honeyProducts?->id,
                'name_en' => 'Wildflower Honey',
                'name_bn' => 'বন্য ফুলের মধু',
                'description_en' => 'Premium wildflower honey with a unique floral taste. Collected from diverse wildflower sources.',
                'description_bn' => 'অনন্য ফুলের স্বাদ সহ প্রিমিয়াম বন্য ফুলের মধু। বিভিন্ন বন্য ফুলের উৎস থেকে সংগ্রহ করা।',
                'price' => 380.00,
                'compare_at_price' => 480.00,
                'sku' => 'WFH-001',
                'stock' => 40,
                'is_active' => true,
                'is_featured' => false,
                'order' => 5,
            ]);

            // Organic Foods Category
            $organicFoods = Category::where('slug', 'organic-foods')->first();

            Product::create([
                'category_id' => $organicFoods?->id,
                'name_en' => 'Organic Quinoa',
                'name_bn' => 'জৈব কিনোয়া',
                'description_en' => 'Premium organic quinoa, a complete protein source. Perfect for healthy meals and salads.',
                'description_bn' => 'প্রিমিয়াম জৈব কিনোয়া, একটি সম্পূর্ণ প্রোটিন উৎস। স্বাস্থ্যকর খাবার এবং সালাদের জন্য নিখুঁত।',
                'price' => 550.00,
                'compare_at_price' => 650.00,
                'buying_price' => 310.00,
                'sku' => 'QNO-001',
                'stock' => 30,
                'is_active' => true,
                'is_featured' => true,
                'order' => 6,
            ]);

            // Health Supplements Category
            $healthSupplements = Category::where('slug', 'health-supplements')->first();

            Product::create([
                'category_id' => $healthSupplements?->id,
                'name_en' => 'Turmeric Powder',
                'name_bn' => 'হলুদ গুঁড়ো',
                'description_en' => 'Pure organic turmeric powder with high curcumin content. Known for anti-inflammatory properties.',
                'description_bn' => 'উচ্চ কারকুমিন সামগ্রী সহ খাঁটি জৈব হলুদ গুঁড়ো। প্রদাহ-বিরোধী বৈশিষ্ট্যের জন্য পরিচিত।',
                'price' => 180.00,
                'compare_at_price' => 220.00,
                'buying_price' => 100.00,
                'sku' => 'TUR-001',
                'stock' => 100,
                'is_active' => true,
                'is_featured' => false,
                'order' => 7,
            ]);

            // Spices & Herbs Category
            $spicesHerbs = Category::where('slug', 'spices-herbs')->first();

            Product::create([
                'category_id' => $spicesHerbs?->id,
                'name_en' => 'Premium Cinnamon',
                'name_bn' => 'প্রিমিয়াম দারুচিনি',
                'description_en' => 'Premium quality Ceylon cinnamon sticks. Rich flavor and aroma, perfect for cooking and beverages.',
                'description_bn' => 'প্রিমিয়াম মানের সিলন দারুচিনি লাঠি। সমৃদ্ধ স্বাদ এবং সুগন্ধ, রান্না এবং পানীয়ের জন্য নিখুঁত।',
                'price' => 250.00,
                'compare_at_price' => 300.00,
                'sku' => 'CIN-001',
                'stock' => 80,
                'is_active' => true,
                'is_featured' => false,
                'order' => 8,
            ]);

            Product::create([
                'category_id' => $spicesHerbs?->id,
                'name_en' => 'Organic Black Pepper',
                'name_bn' => 'জৈব কালো মরিচ',
                'description_en' => 'Premium organic black pepper with strong aroma and flavor. Freshly ground for maximum taste.',
                'description_bn' => 'শক্তিশালী সুগন্ধ এবং স্বাদ সহ প্রিমিয়াম জৈব কালো মরিচ। সর্বোচ্চ স্বাদের জন্য তাজা গুঁড়ো করা।',
                'price' => 200.00,
                'compare_at_price' => 250.00,
                'buying_price' => 112.00,
                'sku' => 'BPP-001',
                'stock' => 90,
                'is_active' => true,
                'is_featured' => false,
                'order' => 9,
            ]);

            // Add 50 dummy products
            Product::factory(50)->create();
        }
    }
}
