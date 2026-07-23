<?php

namespace App\Livewire\Concerns;

use App\Enums\StockMovementType;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use App\Models\ShippingCityRate;
use App\Models\ShippingSetting;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\ShippingService;
use App\Support\PhoneFormats;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

/**
 * Session-backed cart, coupon, shipping estimate, and checkout flow shared by
 * every public-facing Livewire component that lets a visitor buy something.
 */
trait HasShoppingCart
{
    /**
     * Mutated only by this trait's own methods (quickAddToCart, updateCartQuantity,
     * removeFromCart, addToCart/buyNow on LandingPage) which always write through
     * to the session first — #[Locked] so it can't be overwritten directly from
     * the client (e.g. `$wire.set('cart', ...)` from devtools).
     */
    #[Locked]
    public array $cart = [];

    public bool $showCart = false;

    public bool $showCheckout = false;

    public string $couponCode = '';

    /**
     * Set only by applyCoupon() after validating the code — #[Locked] so a
     * client can't skip that validation by setting an arbitrary coupon id.
     */
    #[Locked]
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
            }

            if ($weight === null || (float) $weight === 0.0) {
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
     * The tenant's configured phone format preset (Admin > Website Settings),
     * so checkout's client-side hint/pattern matches the server-side regex
     * instead of assuming Bangladesh for every tenant.
     */
    #[Computed]
    public function phoneFormatPreset(): string
    {
        return Setting::get('phone_format_preset', PhoneFormats::DEFAULT_PRESET);
    }

    #[Computed]
    public function phonePattern(): string
    {
        return PhoneFormats::regexFor($this->phoneFormatPreset);
    }

    #[Computed]
    public function phonePlaceholder(): string
    {
        return PhoneFormats::placeholderFor($this->phoneFormatPreset);
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
        $this->dispatch('cart-updated', count: $this->cartQuantityTotal($cart));

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

        if (! isset($cart[$productId])) {
            return;
        }

        $quantity = (int) $quantity;

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $item = $cart[$productId];
            $maxStock = ! empty($item['product_attribute_id'])
                ? ProductAttribute::find($item['product_attribute_id'])?->stock
                : Product::find($item['id'] ?? $productId)?->stock;

            if ($maxStock !== null) {
                $quantity = min($quantity, max(1, (int) $maxStock));
            }

            $cart[$productId]['quantity'] = $quantity;
        }

        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->dispatch('cart-updated', count: $this->cartQuantityTotal($cart));

        // Force recalculation of shipping cost
        $this->dispatch('$refresh');
    }

    public function removeFromCart($productId): void
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->dispatch('cart-updated', count: $this->cartQuantityTotal($cart));

        // Force recalculation of shipping cost
        $this->dispatch('$refresh');
    }

    private function cartQuantityTotal(array $cart): int
    {
        return array_sum(array_map(fn ($item) => $item['quantity'] ?? 0, $cart));
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

        // The order is built from the session cart and authoritative, freshly
        // fetched product/attribute rows — never from $this->cart or any other
        // client-supplied price/quantity — so a tampered Livewire payload can't
        // change what a customer is charged.
        $sessionCart = session()->get('cart', []);

        if (empty($sessionCart)) {
            session()->flash('error', __('Cart is empty'));

            return;
        }

        $productIds = array_unique(array_column($sessionCart, 'id'));
        $products = Product::where('is_active', true)->whereIn('id', $productIds)->get()->keyBy('id');

        $attributeIds = array_values(array_filter(array_column($sessionCart, 'product_attribute_id')));
        $attributes = $attributeIds
            ? ProductAttribute::where('is_active', true)->whereIn('id', $attributeIds)->get()->keyBy('id')
            : collect();

        $lines = [];
        $unavailable = [];

        foreach ($sessionCart as $cartKey => $item) {
            $product = $products->get($item['id'] ?? null);
            $quantity = max(1, (int) ($item['quantity'] ?? 0));
            $attributeId = $item['product_attribute_id'] ?? null;
            $name = $item['name'] ?? $product?->name ?? __('Item');

            if (! $product) {
                $unavailable[] = $name;

                continue;
            }

            if ($attributeId) {
                $attribute = $attributes->get($attributeId);

                if (! $attribute || $attribute->product_id !== $product->id || $attribute->stock < $quantity) {
                    $unavailable[] = $name;

                    continue;
                }

                $price = (float) $attribute->price;
                $weight = $attribute->getWeightFromAttribute() ?: ((float) ($product->weight_kg ?: 1.0));
            } else {
                if (! $product->isInStock() || $product->stock < $quantity) {
                    $unavailable[] = $name;

                    continue;
                }

                $attribute = null;
                $price = (float) $product->price;
                $weight = (float) ($product->weight_kg ?: 1.0);
            }

            $lines[] = [
                'cart_key' => $cartKey,
                'product' => $product,
                'attribute' => $attribute,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'weight' => $weight,
                'attribute_data' => $item['attribute_data'] ?? $item['variation_data'] ?? null,
            ];
        }

        if (! empty($unavailable)) {
            // Prune the session cart down to the survivors and stop short of
            // creating an order, so the customer sees the corrected cart first.
            $prunedCart = [];
            foreach ($lines as $line) {
                $prunedCart[$line['cart_key']] = $sessionCart[$line['cart_key']];
            }

            session()->put('cart', $prunedCart);
            $this->cart = $prunedCart;
            $this->dispatch('cart-updated', count: $this->cartQuantityTotal($prunedCart));

            session()->flash('error', __('Some items in your cart are no longer available and were removed: :items. Please review your cart and try again.', ['items' => implode(', ', $unavailable)]));

            return;
        }

        $subtotal = collect($lines)->sum(fn ($line) => $line['price'] * $line['quantity']);

        $appliedCoupon = null;
        $discount = 0;

        if ($this->appliedCouponId) {
            $appliedCoupon = Coupon::find($this->appliedCouponId);

            if ($appliedCoupon && $appliedCoupon->isValid()) {
                $discount = $appliedCoupon->calculateDiscount($subtotal);
            } else {
                $appliedCoupon = null;
                $this->appliedCouponId = null;
            }
        }

        $city = City::find($this->shippingCityId);

        $cartWeight = collect($lines)->sum(fn ($line) => $line['weight'] * $line['quantity']);
        $shippingService = app(ShippingService::class);
        $shippingCost = $cartWeight > 0 ? $shippingService->calculate($cartWeight, $this->shippingCityId) : 0;

        $total = max(0, $subtotal - $discount + $shippingCost);

        try {
            $order = DB::transaction(function () use ($lines, $subtotal, $discount, $shippingCost, $total, $city, $appliedCoupon) {
                $warehouse = Warehouse::default();

                // Re-check stock under a row lock inside the transaction, so two
                // concurrent checkouts for the last unit can't both succeed. This
                // locks the WarehouseStock row (the real source of truth) rather
                // than the Product/ProductAttribute row, since those now only hold
                // a denormalized total.
                $warehouseStocks = [];

                foreach ($lines as $line) {
                    $warehouseStock = WarehouseStock::findOrCreateFor($warehouse->id, $line['product']->id, $line['attribute']?->id);
                    $locked = WarehouseStock::whereKey($warehouseStock->id)->lockForUpdate()->first();

                    if (! $locked || $locked->stock < $line['quantity']) {
                        throw new \RuntimeException(__('Sorry, ":name" just sold out. Please update your cart and try again.', ['name' => $line['name']]));
                    }

                    $warehouseStocks[] = $locked;
                }

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

                foreach ($lines as $index => $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $line['product']->id,
                        'product_attribute_id' => $line['attribute']?->id,
                        'warehouse_id' => $warehouse->id,
                        'product_name' => $line['name'],
                        'attribute_data' => $line['attribute_data'],
                        'price' => $line['price'],
                        'quantity' => $line['quantity'],
                        'subtotal' => $line['price'] * $line['quantity'],
                        'stock_deducted' => true,
                    ]);

                    StockMovementContext::run([
                        'type' => StockMovementType::Sale,
                        'reason' => "Order #{$order->order_number}",
                        'changed_by' => Auth::id(),
                    ], function () use ($warehouseStocks, $index, $line) {
                        $warehouseStocks[$index]->decrement('stock', $line['quantity']);
                    });
                }

                $appliedCoupon?->incrementUsage();

                return $order;
            });
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        // Snapshot cart lines for the Facebook Pixel before clearing.
        $cartItemsForPixel = $lines;

        session()->forget('cart');
        $this->cart = [];
        $this->appliedCouponId = null;
        $this->showCheckout = false;
        $this->order = $order->fresh(['items']);

        $this->dispatch('fbq:track', 'Purchase', [
            'value' => $total,
            'currency' => Setting::get('currency_code', 'BDT'),
            'contents' => array_map(fn ($line) => [
                'id' => $line['product']->id,
                'quantity' => $line['quantity'],
            ], $cartItemsForPixel),
        ]);

        $this->showOrderConfirmation = true;
    }
}
