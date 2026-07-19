<?php

declare(strict_types=1);

use App\Livewire\Admin\WebsiteSettings\Footer;
use App\Models\Product;
use App\Models\Setting;
use App\Support\FooterVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('admin can view the footer settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.website.footer'));

    $response->assertOk();
    foreach (FooterVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a footer variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Footer::class)
        ->set('storefront_footer_variant', 'noir')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_footer_variant'))->toBe('noir');
});

test('an unknown footer variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Footer::class)
        ->set('storefront_footer_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_footer_variant']);

    expect(Setting::get('storefront_footer_variant'))->toBeNull();
});

test('the storefront renders every footer variant without error', function () {
    Setting::set('site_name', 'Footer Test Shop');
    Setting::set('contact_email', 'hello@example.com');
    Setting::set('contact_phone', '+8801700000000');
    Setting::set('social_facebook', 'https://facebook.com/example');

    Product::factory()->create([
        'name_en' => 'Footer Test Product',
        'is_active' => true,
        'stock' => 5,
    ]);

    foreach (FooterVariants::keys() as $key) {
        Setting::set('storefront_footer_variant', $key);

        get('http://default.localhost/shop')
            ->assertOk()
            ->assertSee('Footer Test Shop')
            ->assertSee('All rights reserved');
    }
});

test('a stale stored footer variant falls back to the default', function () {
    Setting::set('storefront_footer_variant', 'removed-legacy-variant');

    expect(FooterVariants::resolve(Setting::get('storefront_footer_variant')))->toBe(FooterVariants::DEFAULT);

    get('http://default.localhost/shop')->assertOk();
});
