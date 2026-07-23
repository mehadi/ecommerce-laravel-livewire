<?php

declare(strict_types=1);

use App\Livewire\Admin\LandingPages\Edit;
use App\Livewire\LandingPage;
use App\Models\LandingPageConfig;
use App\Models\LandingPageSection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeLandingPageProduct(): Product
{
    return Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);
}

test('normalizedBlocks falls back to the historical order and visibility from legacy config keys', function () {
    $config = new LandingPageConfig;
    $config->config = [
        'show_trust_badges' => true,
        'show_product_details' => true,
        'show_features' => true,
        'features_section_ids' => [5, 6],
        'show_testimonials' => true,
        'show_faq' => false,
        'show_cta' => true,
    ];

    $blocks = $config->normalizedBlocks();

    expect(array_column($blocks, 'type'))->toBe([
        'trust_badges', 'product_details', 'features', 'testimonials',
        'about', 'benefits', 'contact', 'products', 'faq', 'cta',
    ]);
    expect($blocks[2]['section_ids'])->toBe([5, 6]);
    expect(collect($blocks)->firstWhere('type', 'faq')['enabled'])->toBeFalse();
    expect(collect($blocks)->whereIn('type', ['about', 'benefits', 'contact', 'products'])->pluck('enabled')->unique()->all())
        ->toBe([false]);
});

test('normalizedBlocks uses the blocks key directly when present', function () {
    $config = new LandingPageConfig;
    $config->config = [
        'blocks' => [
            ['type' => 'faq', 'enabled' => true, 'section_ids' => []],
            ['type' => 'features', 'enabled' => false, 'section_ids' => []],
        ],
    ];

    expect($config->normalizedBlocks())->toBe($config->config['blocks']);
});

test('a legacy landing page (no blocks key) renders sections in the historical order and respects show_faq=false', function () {
    $product = makeLandingPageProduct();

    LandingPageSection::create([
        'type' => 'features', 'title_en' => 'Rich in Nutrients', 'content_en' => 'Packed with minerals.',
        'is_active' => true, 'order' => 1,
    ]);

    $landingPage = LandingPageConfig::create([
        'name' => 'Legacy Page', 'slug' => 'legacy-page', 'product_id' => $product->id,
        'config' => ['show_features' => true, 'show_faq' => false],
        'is_active' => true, 'order' => 0,
    ]);

    $html = Livewire::test(LandingPage::class, ['slug' => $landingPage->slug])->html();

    expect($html)->toContain('id="features"');
    expect($html)->not->toContain('id="faq"');
});

test('admin can reorder page blocks and the public page reflects the new order', function () {
    $admin = actingAsAdmin();
    $product = makeLandingPageProduct();

    LandingPageSection::create([
        'type' => 'features', 'title_en' => 'Feature One', 'content_en' => 'Feature content.',
        'is_active' => true, 'order' => 1,
    ]);
    LandingPageSection::create([
        'type' => 'faq', 'title_en' => 'A question?', 'content_en' => 'An answer.',
        'is_active' => true, 'order' => 1,
    ]);

    $landingPage = LandingPageConfig::create([
        'name' => 'Reorder Page', 'slug' => 'reorder-page', 'product_id' => $product->id,
        'config' => ['blocks' => LandingPageConfig::defaultBlocks()],
        'is_active' => true, 'order' => 0,
    ]);

    // Move 'faq' ahead of 'features' in the block order.
    $newOrder = collect(LandingPageConfig::defaultBlocks())->pluck('type')
        ->reject(fn ($type) => $type === 'faq')
        ->flatMap(fn ($type) => $type === 'features' ? ['faq', 'features'] : [$type])
        ->values()
        ->all();

    Livewire::actingAs($admin)
        ->test(Edit::class, ['landingPage' => $landingPage])
        ->call('updateBlockOrder', $newOrder)
        ->call('update');

    $landingPage->refresh();
    expect(array_column($landingPage->config['blocks'], 'type'))->toBe($newOrder);

    $html = Livewire::test(LandingPage::class, ['slug' => $landingPage->slug])->html();

    expect(strpos($html, 'id="faq"'))->toBeLessThan(strpos($html, 'id="features"'));
});

test('disabling a block hides it and enabling an about block renders the generic content section', function () {
    $product = makeLandingPageProduct();

    LandingPageSection::create([
        'type' => 'about', 'title_en' => 'Our Story', 'content_en' => 'We started in Dhaka.',
        'is_active' => true, 'order' => 1,
    ]);

    $blocks = collect(LandingPageConfig::defaultBlocks())->map(function ($block) {
        if ($block['type'] === 'faq') {
            $block['enabled'] = false;
        }
        if ($block['type'] === 'about') {
            $block['enabled'] = true;
        }

        return $block;
    })->all();

    $landingPage = LandingPageConfig::create([
        'name' => 'About Page', 'slug' => 'about-page', 'product_id' => $product->id,
        'config' => ['blocks' => $blocks],
        'is_active' => true, 'order' => 0,
    ]);

    $html = Livewire::test(LandingPage::class, ['slug' => $landingPage->slug])->html();

    expect($html)->not->toContain('id="faq"');
    expect($html)->toContain('Our Story');
});
