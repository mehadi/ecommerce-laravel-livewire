<?php

declare(strict_types=1);

use App\Livewire\Admin\Products\Create;
use App\Livewire\Admin\Products\Edit;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function actingAsAdmin(): User
{
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    return $admin;
}

test('admin can create a product with rich descriptions intact', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();

    $richTextEn = '<h2>Daily Glow Ritual</h2><ul><li>Boosts radiance</li><li>Locks in hydration</li></ul>';
    $richTextBn = '<p><strong>ত্বকের উজ্জ্বলতা</strong> ও আর্দ্রতা ধরে রাখে।</p>';

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('category_id', $category->id)
        ->set('name_en', 'Radiant Serum Concentrate')
        ->set('name_bn', 'রেডিয়ান্ট সিরাম কনসেনট্রেট')
        ->set('description_en', $richTextEn)
        ->set('description_bn', $richTextBn)
        ->set('ingredients_en', 'Vitamin C, Hyaluronic Acid, Niacinamide')
        ->set('ingredients_bn', 'ভিটামিন সি, হায়ালুরোনিক অ্যাসিড, নিয়াসিনামাইড')
        ->set('benefits_en', 'Brightens complexion and smooths texture.')
        ->set('benefits_bn', 'ত্বকের উজ্জ্বলতা ও টেক্সচার উন্নত করে।')
        ->set('price', 1299.50)
        ->set('compare_at_price', 1499.00)
        ->set('sku', 'RDS-001')
        ->set('stock', 25)
        ->set('order', 3)
        ->set('is_active', true)
        ->set('is_featured', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('sku', 'RDS-001')->first();

    expect($product)->not->toBeNull();
    expect($product)
        ->description_en->toBe($richTextEn)
        ->description_bn->toBe($richTextBn)
        ->is_featured->toBeTrue();
});

test('admin can update product rich descriptions without stripping markup', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Hydrating Elixir',
        'description_en' => '<p>Original description</p>',
        'description_bn' => '<p>মূল বর্ণনা</p>',
        'sku' => 'HYD-123',
        'price' => 990,
        'stock' => 10,
        'is_featured' => false,
    ]);

    $updatedEn = '<h3>New Glow Formula</h3><p><strong>Clinically proven</strong> to boost luminosity in 7 days.</p>';
    $updatedBn = '<p><em>দ্রুত ফলাফল</em> এবং ত্বকের দীপ্তি বাড়ায়।</p>';

    Livewire::actingAs($admin)
        ->test(Edit::class, ['product' => $product])
        ->set('name_en', 'Hydrating Elixir Plus')
        ->set('description_en', $updatedEn)
        ->set('description_bn', $updatedBn)
        ->set('is_featured', true)
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product->refresh();

    expect($product)
        ->name_en->toBe('Hydrating Elixir Plus')
        ->description_en->toBe($updatedEn)
        ->description_bn->toBe($updatedBn)
        ->is_featured->toBeTrue();
});

test('rich text attachments persist to public disk', function () {
    Storage::fake('public');

    $admin = actingAsAdmin();

    $component = Livewire::actingAs($admin)
        ->test(Create::class);

    $upload = UploadedFile::fake()->image('editor-attachment.jpg', 640, 640);

    $component->set('pendingAttachments', [$upload]);

    $component->call('storePendingAttachment');

    $payload = data_get($component->effects, 'returns.0');

    expect($payload)
        ->toBeArray()
        ->and($payload)
        ->toHaveKeys(['url', 'path']);

    Storage::disk('public')->assertExists($payload['path']);

    expect($component->instance()->pendingAttachments)->toBe([]);
});
