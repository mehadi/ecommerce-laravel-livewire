<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserDashboardPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('provides actionable dashboard insights and supports resetting preferences', function () {
    $user = User::factory()->create();
    actingAs($user);

    $category = Category::factory()->create(['name_en' => 'Supplements']);

    $primaryProduct = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Immunity Boost',
        'price' => 1200,
        'stock' => 50,
        'is_active' => true,
    ]);

    $secondaryProduct = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Energy Plus',
        'price' => 900,
        'stock' => 80,
        'is_active' => true,
    ]);

    $firstOrder = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '01700000000',
        'shipping_address' => '123 Main Street',
        'shipping_city' => 'Dhaka',
        'shipping_postal_code' => '1200',
        'subtotal' => 2400,
        'discount' => 200,
        'total' => 2200,
        'advance_payment' => 500,
        'payment_method' => 'cash_on_delivery',
        'status' => 'processing',
        'notes' => null,
    ]);

    $firstOrder->created_at = Carbon::now()->subDays(3);
    $firstOrder->updated_at = Carbon::now()->subDays(3);
    $firstOrder->save();

    $firstOrder->items()->create([
        'product_id' => $primaryProduct->id,
        'product_name' => $primaryProduct->name_en,
        'price' => 1200,
        'quantity' => 2,
        'subtotal' => 2400,
    ]);

    $secondOrder = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '01700000000',
        'shipping_address' => '456 Secondary Road',
        'shipping_city' => 'Chattogram',
        'shipping_postal_code' => '4000',
        'subtotal' => 900,
        'discount' => 0,
        'total' => 900,
        'advance_payment' => 0,
        'payment_method' => 'bkash',
        'status' => 'pending',
        'notes' => null,
    ]);

    $secondOrder->created_at = Carbon::now()->subDay();
    $secondOrder->updated_at = Carbon::now()->subDay();
    $secondOrder->save();

    $secondOrder->items()->create([
        'product_id' => $secondaryProduct->id,
        'product_name' => $secondaryProduct->name_en,
        'price' => 900,
        'quantity' => 1,
        'subtotal' => 900,
    ]);

    $component = Livewire::test(Dashboard::class);

    $insights = $component->instance()->dashboardInsights();

    expect($insights)
        ->toBeArray()
        ->not->toBeEmpty();

    expect(collect($insights)->pluck('title'))->toContain(__('Revenue Trend'));
    expect(collect($insights)->pluck('body')->join(' '))->toContain('Immunity Boost');
    expect(collect($insights)->pluck('title'))->toContain(__('Fulfillment Focus'));

    $statusSummary = $component->instance()->statusSummary();
    expect($statusSummary)
        ->toBeArray()
        ->not->toBeEmpty();
    expect(collect($statusSummary)->firstWhere('status', 'processing')['count'])->toBe(1);

    $recentOrders = $component->instance()->recentOrders();
    expect($recentOrders)->toHaveCount(2);
    expect($recentOrders->first()->status)->toBe('pending');
    expect($recentOrders->first()->items->sum('quantity'))->toBe(1);

    // Toggle a chart card off and ensure persistence updates
    $component->call('toggleCardVisibility', 'revenue_chart');

    expect(
        UserDashboardPreference::where('user_id', $user->id)
            ->where('card_key', 'revenue_chart')
            ->value('is_visible')
    )->toBeFalse();

    // Reset and confirm defaults are restored
    $component->call('resetDashboardPreferences');

    $expectedPreferenceCount = count($component->instance()->availableMetricCards())
        + count($component->instance()->availableInsightCards())
        + count($component->instance()->availableChartCards());

    expect(
        UserDashboardPreference::where('user_id', $user->id)->count()
    )->toBe($expectedPreferenceCount);

    expect(
        UserDashboardPreference::where('user_id', $user->id)
            ->where('card_key', 'revenue_chart')
            ->value('is_visible')
    )->toBeTrue();
});
