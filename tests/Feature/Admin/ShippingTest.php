<?php

declare(strict_types=1);

use App\Livewire\Admin\Shipping\Index;
use App\Models\City;
use App\Models\ShippingCityRate;
use App\Models\ShippingSetting;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('admin can access shipping management page', function () {
    $admin = actingAsAdmin();

    $this->actingAs($admin)
        ->get(route('admin.shipping.index'))
        ->assertSuccessful()
        ->assertSeeLivewire(Index::class);
});

test('admin can create flat shipping setting', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'flat')
        ->set('flatRate', 50.00)
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors();

    $setting = ShippingSetting::where('is_active', true)->first();
    expect($setting)->not->toBeNull()
        ->and($setting->type)->toBe('flat')
        ->and($setting->flat_rate)->toBe(50.00);
});

test('admin can create weight-based shipping setting', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'weight')
        ->set('baseWeightKg', 2.00)
        ->set('baseRate', 30.00)
        ->set('perKgRate', 10.00)
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors();

    $setting = ShippingSetting::where('is_active', true)->first();
    expect($setting)->not->toBeNull()
        ->and($setting->type)->toBe('weight')
        ->and($setting->base_rate)->toBe(30.00)
        ->and($setting->per_kg_rate)->toBe(10.00);
});

test('admin can create city-based shipping setting', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'city')
        ->set('baseWeightKg', 1.00)
        ->set('baseRate', 40.00)
        ->set('perKgRate', 15.00)
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors();

    $setting = ShippingSetting::where('is_active', true)->first();
    expect($setting)->not->toBeNull()
        ->and($setting->type)->toBe('city')
        ->and($setting->base_rate)->toBe(40.00)
        ->and($setting->per_kg_rate)->toBe(15.00);
});

test('admin can add city-specific rates', function () {
    $admin = actingAsAdmin();
    $city = City::factory()->create(['is_active' => true]);

    // First, set shipping type to city
    ShippingSetting::create([
        'type' => 'city',
        'base_weight_kg' => 1.00,
        'base_rate' => 40.00,
        'per_kg_rate' => 15.00,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'city')
        ->call('openCityRateModal')
        ->set('selectedCityId', $city->id)
        ->set('cityBaseRate', 50.00)
        ->set('cityPerKgRate', 20.00)
        ->set('cityBaseWeightKg', 1.50)
        ->set('cityIsActive', true)
        ->call('saveCityRate')
        ->assertHasNoErrors();

    $cityRate = ShippingCityRate::where('city_id', $city->id)->first();
    expect($cityRate)->not->toBeNull()
        ->and($cityRate->base_rate)->toBe(50.00)
        ->and($cityRate->per_kg_rate)->toBe(20.00)
        ->and($cityRate->base_weight_kg)->toBe(1.50);
});

test('admin can edit city-specific rates', function () {
    $admin = actingAsAdmin();
    $city = City::factory()->create(['is_active' => true]);

    ShippingSetting::create([
        'type' => 'city',
        'base_weight_kg' => 1.00,
        'base_rate' => 40.00,
        'per_kg_rate' => 15.00,
        'is_active' => true,
    ]);

    $cityRate = ShippingCityRate::create([
        'city_id' => $city->id,
        'base_rate' => 50.00,
        'per_kg_rate' => 20.00,
        'base_weight_kg' => 1.50,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'city')
        ->call('openCityRateModal', $cityRate->id)
        ->set('cityBaseRate', 60.00)
        ->set('cityPerKgRate', 25.00)
        ->call('saveCityRate')
        ->assertHasNoErrors();

    $cityRate->refresh();
    expect($cityRate->base_rate)->toBe(60.00)
        ->and($cityRate->per_kg_rate)->toBe(25.00);
});

test('admin can delete city-specific rates', function () {
    $admin = actingAsAdmin();
    $city = City::factory()->create(['is_active' => true]);

    ShippingSetting::create([
        'type' => 'city',
        'base_weight_kg' => 1.00,
        'base_rate' => 40.00,
        'per_kg_rate' => 15.00,
        'is_active' => true,
    ]);

    $cityRate = ShippingCityRate::create([
        'city_id' => $city->id,
        'base_rate' => 50.00,
        'per_kg_rate' => 20.00,
        'base_weight_kg' => 1.50,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'city')
        ->call('deleteCityRate', $cityRate->id)
        ->assertHasNoErrors();

    expect(ShippingCityRate::find($cityRate->id))->toBeNull();
});

test('admin can toggle city rate status', function () {
    $admin = actingAsAdmin();
    $city = City::factory()->create(['is_active' => true]);

    ShippingSetting::create([
        'type' => 'city',
        'base_weight_kg' => 1.00,
        'base_rate' => 40.00,
        'per_kg_rate' => 15.00,
        'is_active' => true,
    ]);

    $cityRate = ShippingCityRate::create([
        'city_id' => $city->id,
        'base_rate' => 50.00,
        'per_kg_rate' => 20.00,
        'base_weight_kg' => 1.50,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'city')
        ->call('toggleCityRateStatus', $cityRate->id)
        ->assertHasNoErrors();

    $cityRate->refresh();
    expect($cityRate->is_active)->toBeFalse();
});

test('shipping service calculates flat rate correctly', function () {
    ShippingSetting::create([
        'type' => 'flat',
        'flat_rate' => 50.00,
        'is_active' => true,
    ]);

    $service = new ShippingService;
    $cost = $service->calculate(5.0);

    expect($cost)->toBe(50.00);
});

test('shipping service calculates weight-based rate correctly', function () {
    ShippingSetting::create([
        'type' => 'weight',
        'base_weight_kg' => 2.00,
        'base_rate' => 30.00,
        'per_kg_rate' => 10.00,
        'is_active' => true,
    ]);

    $service = new ShippingService;

    // Weight within base weight
    expect($service->calculate(1.5))->toBe(30.00);

    // Weight above base weight (2.5kg - 2kg = 0.5kg, rounded up to 1kg)
    expect($service->calculate(2.5))->toBe(40.00); // 30 + (1 * 10)

    // Weight well above base weight (5kg - 2kg = 3kg)
    expect($service->calculate(5.0))->toBe(60.00); // 30 + (3 * 10)
});

test('shipping service calculates city-based rate correctly', function () {
    $city = City::factory()->create(['is_active' => true]);

    ShippingSetting::create([
        'type' => 'city',
        'base_weight_kg' => 1.00,
        'base_rate' => 40.00,
        'per_kg_rate' => 15.00,
        'is_active' => true,
    ]);

    ShippingCityRate::create([
        'city_id' => $city->id,
        'base_rate' => 50.00,
        'per_kg_rate' => 20.00,
        'base_weight_kg' => 1.50,
        'is_active' => true,
    ]);

    $service = new ShippingService;

    // City-specific rate (2.0kg - 1.5kg base = 0.5kg, rounded up to 1kg)
    expect($service->calculate(2.0, $city->id))->toBe(70.00); // 50 + (1 * 20)

    // Fallback to default when city not found
    expect($service->calculate(2.0, 999))->toBe(55.00); // 40 + (1 * 15)
});

test('shipping service returns zero when no active setting', function () {
    ShippingSetting::create([
        'type' => 'flat',
        'flat_rate' => 50.00,
        'is_active' => false,
    ]);

    $service = new ShippingService;
    $cost = $service->calculate(5.0);

    expect($cost)->toBe(0.00);
});

test('validation works for flat shipping', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'flat')
        ->set('flatRate', '')
        ->call('save')
        ->assertHasErrors(['flatRate']);
});

test('validation works for weight-based shipping', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'weight')
        ->set('baseRate', '')
        ->set('perKgRate', '')
        ->call('save')
        ->assertHasErrors(['baseRate', 'perKgRate']);
});

test('validation works for city rate', function () {
    $admin = actingAsAdmin();
    $city = City::factory()->create(['is_active' => true]);

    ShippingSetting::create([
        'type' => 'city',
        'base_weight_kg' => 1.00,
        'base_rate' => 40.00,
        'per_kg_rate' => 15.00,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('type', 'city')
        ->call('openCityRateModal')
        ->set('selectedCityId', '')
        ->call('saveCityRate')
        ->assertHasErrors(['selectedCityId']);
});
