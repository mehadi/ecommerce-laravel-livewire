<?php

namespace App\Livewire\Concerns;

use App\Models\Attribute;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use App\Models\ShippingCityRate;
use App\Models\ShippingSetting;
use App\Services\ShippingService;
use App\Support\PhoneFormats;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;

/**
 * Session-backed cart, coupon, shipping estimate, and checkout flow shared by
 * every public-facing Livewire component that lets a visitor buy something.
 */
trait HasShoppingCart
{
    public array $cart = [];

    public bool $showCart = false;

    public bool $showCheckout = false;

    public string $couponCode = '';

    public ?int $appliedCouponId = null;

    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    public string $shippingAddress = '';

    public string $shippingCity = '';

    public ?int $shippingCityId = null;

    public string $shippingPostalCode = '';

    public string $notes = '';

    public bool $showOrderConfirmation = false;

    public ?Order $order = null;

    public function mountHasShoppingCart(): void
    {
        $this->cart = session()->get('cart', []);
    }

    public function getCartTotalProperty(): float
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    public function getCartSubtotalProperty(): float
    {
        return $this->cartTotal;
    }

    public function getCartDiscountProperty(): float
    {
        $coupon = $this->appliedCoupon;

        if (! $coupon) {
            return 0;
        }

        return $coupon->calculateDiscount($this->cartSubtotal);
    }

    public function getCartWeightProperty(): float
    {
        if (empty($this->cart)) {
            return 0;
        }

        $productIds = [];
        foreach ($this->cart as $item) {
            if (isset($item['id'])) {
                $productIds[] = $item['id'];
            }
        }

        if (empty($productIds)) {
            return 0;
        }

        $productIds = array_unique($productIds);
        $products = Product::whereIn('id', $productIds)->get(['id', 'weight_kg']);

        $productAttributeIds = [];
        foreach ($this->cart as $item) {
            if (isset($item['product_attribute_id'])) {
                $productAttributeIds[] = $item['product_attribute_id'];
            }
        }

        $productAttributes = [];
        if (! empty($productAttributeIds)) {
            $productAttributes = ProductAttribute::whereIn('id', $productAttributeIds)
                ->get(['id', 'weight_kg', 'attribute_data'])
                ->keyBy('id');
        }

        $totalWeight = 0;
        foreach ($this->cart as $item) {
            if (! isset($item['id']) || ! isset($item['quantity'])) {
                continue;
            }

            $productId = $item['id'];
            $quantity = (int) $item['quantity'];
            $weight = 1.00;

            if (isset($item['product_attribute_id']) && isset($productAttributes[$item['product_attribute_id']])) {
                $productAttribute = $productAttributes[$item['product_attribute_id']];
                $weight = $productAttribute->getWeightFromAttribute();

                if ($weight === null && ! empty($productAttribute->attribute_data)) {
                    foreach ($productAttribute->attribute_data as $key => $value) {
                        $attribute = Attribute::where('slug', strtolower($key))
                            ->orWhere('name', $key)
                            ->first();

                        if ($attribute && $attribute->isWeight()) {
                            $weight = (float) $value;
                            break;
                        }
                    }
                }
            }

            if ($weight === null || $weight === 0) {
                $product = $products->firstWhere('id', $productId);
                $weight = $product && $product->weight_kg ? (float) $product->weight_kg : 1.00;
            }

            $totalWeight += $weight * $quantity;
        }

        return $totalWeight;
    }

    public function getShippingCostProperty(): float
    {
        if (empty($this->cart)) {
            return 0;
        }

        $weight = $this->cartWeight;

        if ($weight <= 0) {
            return 0;
        }

        $shippingService = app(ShippingService::class);

        return $shippingService->calculate($weight, $this->shippingCityId);
    }

    #[Computed]
    public function getShippingDetailsProperty(): array
    {
        if (empty($this->cart)) {
            return [];
        }

        $weight = $this->cartWeight;

        if ($weight <= 0) {
            return [];
        }

        $setting = ShippingSetting::getActive();

        if (! $setting || ! $setting->is_active) {
            return [];
        }

        $cityId = $this->shippingCityId;
        $details = [
            'type' => $setting->type,
            'weight' => $weight,
            'total' => $this->shippingCost,
        ];

        if ($setting->type === 'flat') {
            $details['description'] = __('Flat Rate Shipping');
            $details['rate'] = $setting->flat_rate;
        } elseif ($setting->type === 'weight') {
            $baseRate = (float) ($setting->base_rate ?? 0);
            $baseWeight = (float) ($setting->base_weight_kg ?? 1);
            $perKgRate = (float) ($setting->per_kg_rate ?? 0);

            if ($weight <= $baseWeight) {
                $details['description'] = __('Weight-based Shipping (Base Rate)');
                $details['base_rate'] = $baseRate;
                $details['base_weight'] = $baseWeight;
            } else {
                $additionalWeight = $weight - $baseWeight;
                $additionalCost = ceil($additionalWeight) * $perKgRate;
                $details['description'] = __('Weight-based Shipping');
                $details['base_rate'] = $baseRate;
                $details['base_weight'] = $baseWeight;
                $details['additional_weight'] = $additionalWeight;
                $details['additional_cost'] = $additionalCost;
                $details['per_kg_rate'] = $perKgRate;
            }
        } elseif ($setting->type === 'city') {
            $cityRate = null;
            $cityName = null;

            if ($cityId) {
                $cityRate = ShippingCityRate::where('city_id', $cityId)
                    ->where('is_active', true)
                    ->first();
                if ($cityRate) {
                    $city = City::find($cityId);
                    $cityName = $city?->name;
                }
            }

            if (! $cityRate) {
                $cityRate = ShippingCityRate::whereNull('city_id')
                    ->where('is_active', true)
                    ->first();
                if ($cityRate) {
                    $cityName = __('Rest of All Cities');
                }
            }

            if (! $cityRate) {
                $baseRate = (float) ($setting->base_rate ?? 0);
                $baseWeight = (float) ($setting->base_weight_kg ?? 1);
                $perKgRate = (float) ($setting->per_kg_rate ?? 0);

                if ($weight <= $baseWeight) {
                    $details['description'] = __('City-based Shipping (Default Rate)');
                    $details['base_rate'] = $baseRate;
                    $details['base_weight'] = $baseWeight;
                } else {
                    $additionalWeight = $weight - $baseWeight;
                    $additionalCost = ceil($additionalWeight) * $perKgRate;
                    $details['description'] = __('City-based Shipping (Default Rate)');
                    $details['base_rate'] = $baseRate;
                    $details['base_weight'] = $baseWeight;
                    $details['additional_weight'] = $additionalWeight;
                    $details['additional_cost'] = $additionalCost;
                    $details['per_kg_rate'] = $perKgRate;
                }
            } else {
                $baseRate = (float) $cityRate->base_rate;
                $baseWeight = (float) $cityRate->base_weight_kg;
                $perKgRate = (float) $cityRate->per_kg_rate;

                $details['city_name'] = $cityName;
                $details['description'] = __('City-based Shipping');

                if ($weight <= $baseWeight) {
                    $details['base_rate'] = $baseRate;
                    $details['base_weight'] = $baseWeight;
                } else {
                    $additionalWeight = $weight - $baseWeight;
                    $additionalCost = ceil($additionalWeight) * $perKgRate;
                    $details['base_rate'] = $baseRate;
                    $details['base_weight'] = $baseWeight;
                    $details['additional_weight'] = $additionalWeight;
                    $details['additional_cost'] = $additionalCost;
                    $details['per_kg_rate'] = $perKgRate;
                }
            }
        }

        return $details;
    }

    public function getCartFinalTotalProperty(): float
    {
        return max(0, $this->cartSubtotal - $this->cartDiscount + $this->shippingCost);
    }

    #[Computed]
    public function getAppliedCouponProperty(): ?Coupon
    {
        if (! $this->appliedCouponId) {
            return null;
        }

        return Coupon::find($this->appliedCouponId);
    }

    #[Computed]
    public function getCitiesProperty()
    {
        return Cache::remember('cities.active', 3600, function () {
            return City::active()->ordered()->get();
        });
    }

    /**
     * One-click add of a single-variant product from a product card/grid.
     * Products with attributes are not supported here — those require a
     * variant picker, which only exists on the product detail page
     * (see LandingPage::addToCart()/resolveCartEntry()).
     */
    public function quickAddToCart(int $productId): void
    {
        $product = Product::where('is_active', true)->findOrFail($productId);

        if ($product->hasAttributes() || ! $product->isInStock()) {
            return;
        }

        $cart = session()->get('cart', []);
        $existingQty = $cart[$productId]['quantity'] ?? 0;

        if ($existingQty + 1 > $product->stock) {
            session()->flash('error', __('Insufficient stock'));

            return;
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->primary_image,
                'quantity' => 1,
                'attribute_data' => [],
                'product_attribute_id' => null,
            ];
        }

        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->showCart = true;
        $this->dispatch('cart-updated');

        $this->dispatch('fbq:track', 'AddToCart', [
            'content_type' => 'product',
            'content_ids' => [$product->id],
            'value' => (float) $product->price,
            'currency' => Setting::get('currency_code', 'BDT'),
        ]);
    }

    public function updateCartQuantity($productId, $quantity): void
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->dispatch('cart-updated');

        // Force recalculation of shipping cost
        $this->dispatch('$refresh');
    }

    public function removeFromCart($productId): void
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->dispatch('cart-updated');

        // Force recalculation of shipping cost
        $this->dispatch('$refresh');
    }

    public function applyCoupon(): void
    {
        $this->appliedCouponId = null;

        if (empty($this->couponCode)) {
            session()->flash('error', __('Please enter a coupon code'));

            return;
        }

        $coupon = Coupon::where('code', strtoupper($this->couponCode))->first();

        if (! $coupon || ! $coupon->isValid()) {
            session()->flash('error', __('Invalid or expired coupon code'));

            return;
        }

        $this->appliedCouponId = $coupon->id;
        session()->flash('success', __('Coupon applied successfully'));
    }

    public function placeOrder(): void
    {
        $phonePreset = Setting::get('phone_format_preset', PhoneFormats::DEFAULT_PRESET);

        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'nullable|email|max:255',
            'customerPhone' => ['required', 'regex:/'.PhoneFormats::regexFor($phonePreset).'/'],
            'shippingAddress' => 'required|string',
            'shippingCityId' => 'required|exists:cities,id',
            'shippingPostalCode' => 'nullable|string|max:20',
        ], [
            'customerPhone.regex' => __('Please enter a valid phone number (e.g., :example)', ['example' => PhoneFormats::placeholderFor($phonePreset)]),
        ]);

        if (empty($this->cart)) {
            session()->flash('error', __('Cart is empty'));

            return;
        }

        $city = City::find($this->shippingCityId);
        $subtotal = $this->cartSubtotal;
        $discount = $this->appliedCouponId ? $this->cartDiscount : 0;

        $cartWeight = $this->cartWeight;
        $shippingService = app(ShippingService::class);
        $shippingCost = $shippingService->calculate($cartWeight, $this->shippingCityId);

        $total = max(0, $subtotal - $discount + $shippingCost);

        $order = Order::create([
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail ?: '',
            'customer_phone' => $this->customerPhone,
            'shipping_address' => $this->shippingAddress,
            'shipping_city' => $city?->name ?? '',
            'shipping_postal_code' => $this->shippingPostalCode,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'payment_method' => 'cod',
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $item['name'],
                    'attribute_data' => $item['attribute_data'] ?? $item['variation_data'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                if (isset($item['product_attribute_id'])) {
                    $productAttribute = ProductAttribute::find($item['product_attribute_id']);
                    if ($productAttribute) {
                        $productAttribute->decrement('stock', $item['quantity']);
                    }
                } else {
                    $product->decrement('stock', $item['quantity']);
                }
            }
        }

        // Store cart items for Facebook Pixel before clearing
        $cartItemsForPixel = $this->cart;

        if ($this->appliedCouponId) {
            $coupon = Coupon::find($this->appliedCouponId);
            if ($coupon) {
                $coupon->incrementUsage();
            }
        }

        session()->forget('cart');
        $this->cart = [];
        $this->appliedCouponId = null;
        $this->showCheckout = false;
        $this->order = $order->fresh(['items']);

        $this->dispatch('fbq:track', 'Purchase', [
            'value' => $total,
            'currency' => Setting::get('currency_code', 'BDT'),
            'contents' => array_map(fn ($item) => [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
            ], $cartItemsForPixel),
        ]);

        $this->showOrderConfirmation = true;
    }
}
