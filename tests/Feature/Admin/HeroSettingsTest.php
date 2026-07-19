<?php

declare(strict_types=1);

use App\Livewire\Admin\WebsiteSettings\Hero;
use App\Models\Product;
use App\Models\Setting;
use App\Support\HeroVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('admin can view the hero settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.website.hero'));

    $response->assertOk();
    foreach (HeroVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a hero variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Hero::class)
        ->set('storefront_hero_variant', 'spotlight')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_hero_variant'))->toBe('spotlight');
});

test('admin can edit hero content and it renders on the storefront', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Hero::class)
        ->set('storefront_hero_variant', 'classic')
        ->set('hero_badge_text', 'Handmade in Dhaka')
        ->set('hero_primary_cta_label', 'Buy Today')
        ->set('hero_primary_cta_url', '#order')
        ->set('hero_secondary_cta_label', 'See Catalog')
        ->set('hero_secondary_cta_url', '/categories')
        ->set('hero_show_stats', false)
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('hero_badge_text'))->toBe('Handmade in Dhaka');
    expect(Setting::get('hero_show_stats'))->toBe('0');

    Product::factory()->create([
        'name_en' => 'Hero Test Product',
        'is_active' => true,
        'is_featured' => true,
    ]);

    $response = get('http://default.localhost/');

    $response->assertOk()
        ->assertSee('Handmade in Dhaka')
        ->assertSee('Buy Today')
        ->assertSee('#order', false)
        ->assertSee('See Catalog')
        ->assertSee('/categories', false)
        ->assertDontSee('Orders Delivered');
});

test('empty hero content fields fall back to the stock labels', function () {
    Product::factory()->create([
        'name_en' => 'Hero Test Product',
        'is_active' => true,
        'is_featured' => true,
    ]);
    Setting::set('storefront_hero_variant', 'classic');

    // The storefront homepage falls back to shop-focused CTAs; the buy-focused
    // "Order Now" default only applies on /lp/{slug} campaign funnels.
    get('http://default.localhost/')
        ->assertOk()
        ->assertSee('Shop Now')
        ->assertSee('Browse Categories')
        ->assertSee('Orders Delivered');
});

test('an unknown hero variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Hero::class)
        ->set('storefront_hero_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_hero_variant']);

    expect(Setting::get('storefront_hero_variant'))->toBeNull();
});

test('the storefront renders every hero variant without errors', function () {
    Product::factory()->create([
        'name_en' => 'Hero Test Product',
        'is_active' => true,
        'is_featured' => true,
    ]);

    foreach (HeroVariants::keys() as $key) {
        Setting::set('storefront_hero_variant', $key);
        Cache::flush();

        // The tenant storefront home only exists on a tenant (sub)domain — a plain
        // `localhost` request matches the domain-scoped platform marketing page.
        get('http://default.localhost/')
            ->assertOk()
            ->assertSee('Hero Test Product');
    }
});

test('a stale stored variant falls back to the default hero', function () {
    Product::factory()->create(['is_active' => true]);
    Setting::set('storefront_hero_variant', 'removed-legacy-variant');

    get('http://default.localhost/')->assertOk();

    expect(HeroVariants::resolve(Setting::get('storefront_hero_variant')))->toBe(HeroVariants::DEFAULT);
});
