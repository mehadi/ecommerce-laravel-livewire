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
                'ingredients_en' => '100% Pure Date Molasses. No additives, preservatives, or artificial ingredients.',
                'ingredients_bn' => '১০০% খাঁটি খেজুর গুড়। কোনো সংযোজন, সংরক্ষণকারী বা কৃত্রিম উপাদান নেই।',
                'benefits_en' => "• Rich in Potassium, Iron, and Magnesium\n• Natural source of energy\n• Helps maintain healthy digestion\n• Low glycemic index\n• Antioxidant properties\n• Supports immune system",
                'benefits_bn' => "• পটাসিয়াম, আয়রন এবং ম্যাগনেসিয়ামে সমৃদ্ধ\n• প্রাকৃতিক শক্তির উৎস\n• স্বাস্থ্যকর হজম বজায় রাখতে সাহায্য করে\n• নিম্ন গ্লাইসেমিক সূচক\n• অ্যান্টিঅক্সিডেন্ট বৈশিষ্ট্য\n• ইমিউন সিস্টেম সমর্থন করে",
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
                'ingredients_en' => '100% Organic Date Syrup',
                'ingredients_bn' => '১০০% জৈব খেজুর সিরাপ',
                'benefits_en' => "• Organic certified\n• Rich in natural sugars\n• Contains essential minerals\n• Great for cooking and baking",
                'benefits_bn' => "• জৈব সার্টিফাইড\n• প্রাকৃতিক চিনিতে সমৃদ্ধ\n• প্রয়োজনীয় খনিজ ধারণ করে\n• রান্না এবং বেকিংয়ের জন্য চমৎকার",
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
                'ingredients_en' => '100% Raw Organic Honey',
                'ingredients_bn' => '১০০% কাঁচা জৈব মধু',
                'benefits_en' => "• Unfiltered and unpasteurized\n• Rich in antioxidants\n• Natural antibacterial properties\n• Great for immunity",
                'benefits_bn' => "• অপরিশোধিত এবং পাস্তুরায়িত নয়\n• অ্যান্টিঅক্সিডেন্টে সমৃদ্ধ\n• প্রাকৃতিক অ্যান্টিব্যাকটেরিয়াল বৈশিষ্ট্য\n• রোগ প্রতিরোধ ক্ষমতার জন্য দুর্দান্ত",
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
                'ingredients_en' => '100% Pure Coconut Palm Sugar',
                'ingredients_bn' => '१००% খাঁটি নারকেল পাম চিনি',
                'benefits_en' => "• Low glycemic index\n• Rich in minerals\n• Natural and unrefined\n• Suitable for diabetics",
                'benefits_bn' => "• নিম্ন গ্লাইসেমিক সূচক\n• খনিজে সমৃদ্ধ\n• প্রাকৃতিক এবং অপরিশোধিত\n• ডায়াবেটিস রোগীদের জন্য উপযুক্ত",
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
                'ingredients_en' => '100% Pure Wildflower Honey',
                'ingredients_bn' => '१००% খাঁটি বন্য ফুলের মধু',
                'benefits_en' => "• Unique floral flavor\n• Rich in vitamins\n• Natural energy booster\n• Great for skin health",
                'benefits_bn' => "• অনন্য ফুলের স্বাদ\n• ভিটামিনে সমৃদ্ধ\n• প্রাকৃতিক শক্তি বৃদ্ধিকারী\n• ত্বকের স্বাস্থ্যের জন্য দুর্দান্ত",
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
                'ingredients_en' => '100% Organic Quinoa',
                'ingredients_bn' => '१००% জৈব কিনোয়া',
                'benefits_en' => "• Complete protein\n• Gluten-free\n• High in fiber\n• Rich in minerals",
                'benefits_bn' => "• সম্পূর্ণ প্রোটিন\n• গ্লুটেন-মুক্ত\n• উচ্চ ফাইবার\n• খনিজে সমৃদ্ধ",
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
                'ingredients_en' => '100% Organic Turmeric Powder',
                'ingredients_bn' => '१००% জৈব হলুদ গুঁড়ো',
                'benefits_en' => "• High curcumin content\n• Anti-inflammatory\n• Antioxidant properties\n• Supports joint health",
                'benefits_bn' => "• উচ্চ কারকুমিন সামগ্রী\n• প্রদাহ-বিরোধী\n• অ্যান্টিঅক্সিডেন্ট বৈশিষ্ট্য\n• জয়েন্ট স্বাস্থ্য সমর্থন করে",
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
                'ingredients_en' => '100% Pure Ceylon Cinnamon',
                'ingredients_bn' => '१००% খাঁটি সিলন দারুচিনি',
                'benefits_en' => "• Ceylon variety\n• Rich flavor\n• Blood sugar support\n• Antioxidant rich",
                'benefits_bn' => "• সিলন জাত\n• সমৃদ্ধ স্বাদ\n• রক্তে শর্করা সমর্থন\n• অ্যান্টিঅক্সিডেন্ট সমৃদ্ধ",
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
                'ingredients_en' => '100% Organic Black Pepper',
                'ingredients_bn' => '१००% জৈব কালো মরিচ',
                'benefits_en' => "• Strong aroma\n• Rich in piperine\n• Digestive support\n• Antioxidant properties",
                'benefits_bn' => "• শক্তিশালী সুগন্ধ\n• পাইপারিনে সমৃদ্ধ\n• হজম সমর্থন\n• অ্যান্টিঅক্সিডেন্ট বৈশিষ্ট্য",
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
