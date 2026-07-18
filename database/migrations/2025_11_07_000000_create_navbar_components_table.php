<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navbar_components', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->unsignedInteger('order_desktop')->default(0);
            $table->unsignedTinyInteger('span_desktop')->default(12);
            $table->boolean('is_visible_desktop')->default(true);
            $table->unsignedInteger('order_mobile')->default(0);
            $table->unsignedTinyInteger('span_mobile')->default(12);
            $table->boolean('is_visible_mobile')->default(true);
            $table->timestamps();
        });

        $showCategories = Setting::get('navigation_show_categories', '0') === '1';
        $showCartButton = Setting::get('navigation_show_cart_button', '1') === '1';
        $showLanguageSwitcher = Setting::get('navigation_show_language_switcher', '1') === '1';

        $now = now();

        $components = [
            ['key' => 'logo', 'label' => 'Logo', 'order_desktop' => 1, 'span_desktop' => 2, 'is_visible_desktop' => true, 'order_mobile' => 1, 'span_mobile' => 12, 'is_visible_mobile' => false],
            ['key' => 'search', 'label' => 'Search', 'order_desktop' => 2, 'span_desktop' => 3, 'is_visible_desktop' => true, 'order_mobile' => 2, 'span_mobile' => 12, 'is_visible_mobile' => true],
            ['key' => 'nav_links', 'label' => 'Nav Links', 'order_desktop' => 3, 'span_desktop' => 3, 'is_visible_desktop' => true, 'order_mobile' => 3, 'span_mobile' => 12, 'is_visible_mobile' => true],
            ['key' => 'categories', 'label' => 'Categories', 'order_desktop' => 4, 'span_desktop' => 2, 'is_visible_desktop' => $showCategories, 'order_mobile' => 4, 'span_mobile' => 12, 'is_visible_mobile' => $showCategories],
            ['key' => 'cart_button', 'label' => 'Cart Button', 'order_desktop' => 5, 'span_desktop' => 1, 'is_visible_desktop' => $showCartButton, 'order_mobile' => 5, 'span_mobile' => 6, 'is_visible_mobile' => $showCartButton],
            ['key' => 'wishlist_button', 'label' => 'Wishlist / Heart Button', 'order_desktop' => 6, 'span_desktop' => 1, 'is_visible_desktop' => true, 'order_mobile' => 6, 'span_mobile' => 6, 'is_visible_mobile' => false],
            ['key' => 'language_switcher', 'label' => 'Language Switcher', 'order_desktop' => 7, 'span_desktop' => 2, 'is_visible_desktop' => $showLanguageSwitcher, 'order_mobile' => 7, 'span_mobile' => 6, 'is_visible_mobile' => $showLanguageSwitcher],
        ];

        foreach ($components as $component) {
            $component['created_at'] = $now;
            $component['updated_at'] = $now;
            DB::table('navbar_components')->insertOrIgnore($component);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('navbar_components');
    }
};
