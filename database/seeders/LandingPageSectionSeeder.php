<?php

namespace Database\Seeders;

use App\Models\LandingPageSection;
use Illuminate\Database\Seeder;

class LandingPageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hero Section
        LandingPageSection::create([
            'type' => 'hero',
            'title_en' => 'Premium Date Molasses',
            'title_bn' => 'প্রিমিয়াম খেজুর গুড়',
            'content_en' => '100% Pure, Natural Sweetener. Rich in minerals and nutrients. Perfect for health-conscious families.',
            'content_bn' => '১০০% খাঁটি, প্রাকৃতিক মিষ্টি। খনিজ এবং পুষ্টিতে সমৃদ্ধ। স্বাস্থ্য সচেতন পরিবারের জন্য নিখুঁত।',
            'is_active' => true,
            'order' => 1,
        ]);

        // Features Section
        LandingPageSection::create([
            'type' => 'features',
            'title_en' => 'Rich in Nutrients',
            'title_bn' => 'পুষ্টিতে সমৃদ্ধ',
            'content_en' => 'Packed with essential minerals like potassium, iron, and magnesium. Natural source of energy.',
            'content_bn' => 'পটাসিয়াম, আয়রন এবং ম্যাগনেসিয়ামের মতো প্রয়োজনীয় খনিজে ভরপুর। প্রাকৃতিক শক্তির উৎস।',
            'is_active' => true,
            'order' => 1,
        ]);

        LandingPageSection::create([
            'type' => 'features',
            'title_en' => 'Natural & Organic',
            'title_bn' => 'প্রাকৃতিক ও জৈব',
            'content_en' => '100% pure date molasses with no additives, preservatives, or artificial ingredients.',
            'content_bn' => 'কোনো সংযোজন, সংরক্ষণকারী বা কৃত্রিম উপাদান ছাড়াই ১০০% খাঁটি খেজুর গুড়।',
            'is_active' => true,
            'order' => 2,
        ]);

        LandingPageSection::create([
            'type' => 'features',
            'title_en' => 'Low Glycemic Index',
            'title_bn' => 'নিম্ন গ্লাইসেমিক সূচক',
            'content_en' => 'Perfect for those monitoring their blood sugar. Healthier alternative to refined sugar.',
            'content_bn' => 'যারা তাদের রক্তে শর্করা পর্যবেক্ষণ করছেন তাদের জন্য নিখুঁত। পরিশোধিত চিনির স্বাস্থ্যকর বিকল্প।',
            'is_active' => true,
            'order' => 3,
        ]);

        // FAQ Section
        LandingPageSection::create([
            'type' => 'faq',
            'title_en' => 'What is Date Molasses?',
            'title_bn' => 'খেজুর গুড় কি?',
            'content_en' => 'Date molasses is a natural sweetener made by extracting and concentrating the juice from dates. It\'s rich in minerals, vitamins, and antioxidants, making it a healthy alternative to refined sugar.',
            'content_bn' => 'খেজুর গুড় হল একটি প্রাকৃতিক মিষ্টি যা খেজুর থেকে রস নিষ্কাশন এবং ঘন করে তৈরি করা হয়। এটি খনিজ, ভিটামিন এবং অ্যান্টিঅক্সিডেন্টে সমৃদ্ধ, যা এটিকে পরিশোধিত চিনির একটি স্বাস্থ্যকর বিকল্প করে তোলে।',
            'is_active' => true,
            'order' => 1,
        ]);

        LandingPageSection::create([
            'type' => 'faq',
            'title_en' => 'How should I store Date Molasses?',
            'title_bn' => 'খেজুর গুড় কীভাবে সংরক্ষণ করব?',
            'content_en' => 'Store in a cool, dry place. Once opened, keep it refrigerated to maintain freshness. It can last for up to 12 months when stored properly.',
            'content_bn' => 'শীতল, শুষ্ক স্থানে সংরক্ষণ করুন। খোলার পরে, তাজা রাখতে এটি ফ্রিজে রাখুন। সঠিকভাবে সংরক্ষণ করলে এটি ১২ মাস পর্যন্ত স্থায়ী হতে পারে।',
            'is_active' => true,
            'order' => 2,
        ]);

        LandingPageSection::create([
            'type' => 'faq',
            'title_en' => 'Can diabetics use Date Molasses?',
            'title_bn' => 'ডায়াবেটিক রোগীরা খেজুর গুড় ব্যবহার করতে পারেন?',
            'content_en' => 'Date molasses has a lower glycemic index than refined sugar, but it should still be consumed in moderation. We recommend consulting with a healthcare provider before use if you have diabetes.',
            'content_bn' => 'খেজুর গুড়ের গ্লাইসেমিক সূচক পরিশোধিত চিনির তুলনায় কম, তবে এটি এখনও পরিমিতভাবে খাওয়া উচিত। আপনার যদি ডায়াবেটিস থাকে তবে ব্যবহারের আগে একজন স্বাস্থ্যসেবা প্রদানকারীর সাথে পরামর্শ করার পরামর্শ দিই।',
            'is_active' => true,
            'order' => 3,
        ]);

        LandingPageSection::create([
            'type' => 'faq',
            'title_en' => 'What payment methods do you accept?',
            'title_bn' => 'আপনারা কী কী পেমেন্ট পদ্ধতি গ্রহণ করেন?',
            'content_en' => 'We currently accept Cash on Delivery (COD). Payment is made when you receive your order. We may add online payment options in the future.',
            'content_bn' => 'আমরা বর্তমানে ক্যাশ অন ডেলিভারি (COD) গ্রহণ করি। আপনি যখন আপনার অর্ডার গ্রহণ করেন তখন অর্থ প্রদান করা হয়। আমরা ভবিষ্যতে অনলাইন পেমেন্ট বিকল্প যোগ করতে পারি।',
            'is_active' => true,
            'order' => 4,
        ]);

        LandingPageSection::create([
            'type' => 'faq',
            'title_en' => 'How long does delivery take?',
            'title_bn' => 'ডেলিভারি করতে কত সময় লাগে?',
            'content_en' => 'Delivery typically takes 3-5 business days within major cities in Bangladesh. For remote areas, it may take 5-7 business days. You will receive a tracking number once your order is shipped.',
            'content_bn' => 'বাংলাদেশের প্রধান শহরগুলির মধ্যে ডেলিভারি সাধারণত ৩-৫ কার্যদিবস সময় নেয়। দূরবর্তী অঞ্চলের জন্য, এটি ৫-৭ কার্যদিবস সময় নিতে পারে। আপনার অর্ডার পাঠানো হলে আপনি একটি ট্র্যাকিং নম্বর পাবেন।',
            'is_active' => true,
            'order' => 5,
        ]);
    }
}
