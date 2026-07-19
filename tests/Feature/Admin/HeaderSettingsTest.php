<?php

declare(strict_types=1);

use App\Livewire\Admin\WebsiteSettings\Header;
use App\Models\Product;
use App\Models\Setting;
use App\Support\NavbarVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('admin can view the header settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.website.header'));

    $response->assertOk();
    foreach (NavbarVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a header variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Header::class)
        ->set('storefront_header_variant', 'bold')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_header_variant'))->toBe('bold');
});

test('an unknown header variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Header::class)
        ->set('storefront_header_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_header_variant']);

    expect(Setting::get('storefront_header_variant'))->toBeNull();
});

test('the storefront renders every header variant without errors', function () {
    Setting::set('site_name', 'Header Test Shop');

    Product::factory()->create([
        'name_en' => 'Header Test Product',
        'is_active' => true,
        'is_featured' => true,
    ]);

    foreach (NavbarVariants::keys() as $key) {
        Setting::set('storefront_header_variant', $key);
        Cache::flush();

        get('http://default.localhost/')
            ->assertOk()
            ->assertSee('Header Test Shop');
    }
});

test('a stale stored header variant falls back to the default', function () {
    Setting::set('storefront_header_variant', 'removed-legacy-variant');

    expect(NavbarVariants::resolve(Setting::get('storefront_header_variant')))->toBe(NavbarVariants::DEFAULT);

    get('http://default.localhost/')->assertOk();
});
