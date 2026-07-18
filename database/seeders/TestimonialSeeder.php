<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimonial::create([
            'name' => 'Fatima Rahman',
            'location' => 'Dhaka, Bangladesh',
            'content_en' => 'The date molasses has been a game changer for our family. It\'s natural, delicious, and my diabetic husband can enjoy it too! Highly recommend.',
            'content_bn' => 'খেজুর গুড় আমাদের পরিবারের জন্য গেম চেঞ্জার হয়েছে। এটি প্রাকৃতিক, সুস্বাদু এবং আমার ডায়াবেটিস রোগী স্বামীও এটি উপভোগ করতে পারেন! অত্যন্ত সুপারিশ করছি।',
            'rating' => 5,
            'is_active' => true,
            'order' => 1,
        ]);

        Testimonial::create([
            'name' => 'Mohammad Hasan',
            'location' => 'Chittagong, Bangladesh',
            'content_en' => 'Excellent quality product! The taste is amazing and it has helped me reduce my sugar intake significantly. Delivery was quick too.',
            'content_bn' => 'চমৎকার মানের পণ্য! স্বাদ আশ্চর্যজনক এবং এটি আমার চিনি গ্রহণ উল্লেখযোগ্যভাবে হ্রাস করতে সাহায্য করেছে। ডেলিভারিও দ্রুত ছিল।',
            'rating' => 5,
            'is_active' => true,
            'order' => 2,
        ]);

        Testimonial::create([
            'name' => 'Sadia Ahmed',
            'location' => 'Sylhet, Bangladesh',
            'content_en' => 'I use this date molasses in my cooking and baking. It adds a rich, natural sweetness that\'s much better than refined sugar. Love it!',
            'content_bn' => 'আমি আমার রান্না এবং বেকিংয়ে এই খেজুর গুড় ব্যবহার করি। এটি একটি সমৃদ্ধ, প্রাকৃতিক মিষ্টি যোগ করে যা পরিশোধিত চিনির থেকে অনেক ভালো। ভালোবাসি!',
            'rating' => 5,
            'is_active' => true,
            'order' => 3,
        ]);

        Testimonial::create([
            'name' => 'Kamrul Islam',
            'location' => 'Rajshahi, Bangladesh',
            'content_en' => 'Best natural sweetener I\'ve tried! The quality is premium and the price is very reasonable. Will definitely order again.',
            'content_bn' => 'আমি যে প্রাকৃতিক মিষ্টি চেষ্টা করেছি তার মধ্যে সেরা! মান প্রিমিয়াম এবং দাম খুব যুক্তিসঙ্গত। অবশ্যই আবার অর্ডার করব।',
            'rating' => 5,
            'is_active' => true,
            'order' => 4,
        ]);

        Testimonial::create([
            'name' => 'Nusrat Jahan',
            'location' => 'Khulna, Bangladesh',
            'content_en' => 'My kids love it! It\'s a great way to sweeten their food naturally. The packaging is good and the product stays fresh.',
            'content_bn' => 'আমার বাচ্চারা এটা ভালোবাসে! তাদের খাবার প্রাকৃতিকভাবে মিষ্টি করার জন্য এটি একটি দুর্দান্ত উপায়। প্যাকেজিং ভালো এবং পণ্য তাজা থাকে।',
            'rating' => 5,
            'is_active' => true,
            'order' => 5,
        ]);

        Testimonial::create([
            'name' => 'Rashid Ali',
            'location' => 'Barisal, Bangladesh',
            'content_en' => 'As someone who is health conscious, this date molasses is perfect. It\'s natural, has great nutritional value, and tastes wonderful.',
            'content_bn' => 'স্বাস্থ্য সচেতন ব্যক্তি হিসাবে, এই খেজুর গুড় নিখুঁত। এটি প্রাকৃতিক, দুর্দান্ত পুষ্টিগুণ রয়েছে এবং স্বাদে চমৎকার।',
            'rating' => 5,
            'is_active' => true,
            'order' => 6,
        ]);
    }
}
