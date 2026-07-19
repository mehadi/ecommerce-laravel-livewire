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
