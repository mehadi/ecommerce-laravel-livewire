<?php

namespace Database\Seeders;

use App\Models\NavigationItem;
use Illuminate\Database\Seeder;

class NavigationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultItems = [
            [
                'label_en' => 'Home',
                'label_bn' => 'হোম',
                'url' => '/',
                'type' => 'route',
                'route_name' => 'home',
                'order' => 0,
                'is_active' => true,
                'open_in_new_tab' => false,
            ],
            [
                'label_en' => 'Features',
                'label_bn' => 'বৈশিষ্ট্য',
                'url' => '#features',
                'type' => 'section',
                'route_name' => null,
                'order' => 1,
                'is_active' => true,
                'open_in_new_tab' => false,
            ],
            [
                'label_en' => 'Reviews',
                'label_bn' => 'পর্যালোচনা',
                'url' => '#testimonials',
                'type' => 'section',
                'route_name' => null,
                'order' => 2,
                'is_active' => true,
                'open_in_new_tab' => false,
            ],
            [
                'label_en' => 'FAQ',
                'label_bn' => 'প্রশ্নোত্তর',
                'url' => '#faq',
                'type' => 'section',
                'route_name' => null,
                'order' => 3,
                'is_active' => true,
                'open_in_new_tab' => false,
            ],
        ];

        foreach ($defaultItems as $item) {
            NavigationItem::updateOrCreate(
                ['url' => $item['url'], 'type' => $item['type']],
                $item
            );
        }
    }
}
