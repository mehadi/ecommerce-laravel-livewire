<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasShoppingCart;
use App\Models\LandingPageConfig;
use App\Models\LandingPageSection;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LandingPage extends Component
{
    use HasShoppingCart;

    public ?int $productId = null;

    public int $quantity = 1;

    public ?LandingPageConfig $landingPageConfig = null;

    public ?int $selectedProductAttributeId = null;

    public array $selectedAttributeValues = [];

    public function mount(?string $slug = null): void
    {
        if ($slug) {
            $this->landingPageConfig = Cache::remember(Tenancy::cacheKey('landing.page.'.$slug), 3600, function () use ($slug) {
                return LandingPageConfig::where('slug', $slug)
                    ->where('is_active', true)
                    ->with('product')
                    ->first();
            });

            if (! $this->landingPageConfig) {
                abort(404);
            }

            $this->productId = $this->landingPageConfig->product_id
                ?? $this->resolveFallbackProduct()?->id;
        } else {
            $this->productId = $this->resolveFallbackProduct()?->id;
        }
    }

    /**
     * The generic product shown when a route has no specific product tied to
     * it (home-fallback landing page, or a matched config without its own
     * product_id) -- prefers a featured product, falling back to any active one.
     */
    private function resolveFallbackProduct(): ?Product
    {
        return Product::where('is_active', true)->where('is_featured', true)->first()
            ?? Product::where('is_active', true)->first();
    }

    #[Computed]
    public function getProductProperty(): ?Product
    {
        if (! $this->productId) {
            return null;
        }

        return Product::with('productAttributes')->find($this->productId);
    }

    public function updatedProductId(): void
    {
        // Reset selections when product changes
        $this->quantity = 1;
        $this->selectedAttributeValues = [];
        $this->selectedProductAttributeId = null;
    }

    /**
     * Attribute names used by the current product's combinations, derived
     * from already-loaded attribute_data (no query).
     *
     * @return array<int, string>
     */
    private function productAttributeNames(): array
    {
        if (! $this->product) {
            return [];
        }

        return $this->product->productAttributes
            ->pluck('attribute_data')
            ->flatMap(fn ($data) => array_keys($data ?? []))
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Active Attribute rows (with their active values eager-loaded) for the
     * current product's combinations. Feeds the attribute picker partial so
     * it doesn't run its own query on every attribute click / quantity change.
     */
    #[Computed]
    public function getPickerAttributesProperty()
    {
        $attributeNames = $this->productAttributeNames();

        if (empty($attributeNames)) {
            return collect();
        }

        return \App\Models\Attribute::whereIn('name', $attributeNames)
            ->where('is_active', true)
            ->orderBy('order')
            ->with('activeValues')
            ->get();
    }

    /**
     * Attribute rows (all values, not just active -- matches the un-scoped
     * lookup selectAttributeValue() used to run per selection) keyed by
     * name, loaded once per request instead of inside the matching loop.
     */
    #[Computed]
    public function getAttributeLookupByNameProperty()
    {
        $attributeNames = $this->productAttributeNames();

        if (empty($attributeNames)) {
            return collect();
        }

        return \App\Models\Attribute::whereIn('name', $attributeNames)
            ->with('values')
            ->get()
            ->keyBy('name');
    }

    public function selectAttributeValue(string $attributeName, string $value): void
    {
        // Update selected attribute values
        $this->selectedAttributeValues[$attributeName] = $value;
        $this->selectedProductAttributeId = null;

        // Find matching product attribute if all required attributes are selected
        if (! $this->product || ! $this->product->hasAttributes()) {
            return;
        }

        // Get all unique attribute names from product attributes
        $attributeNames = $this->product->productAttributes
            ->pluck('attribute_data')
            ->flatMap(fn ($data) => array_keys($data ?? []))
            ->unique()
            ->toArray();

        // Check if we have all required attributes selected
        $hasAllSelections = count($this->selectedAttributeValues) === count($attributeNames)
            && empty(array_diff($attributeNames, array_keys($this->selectedAttributeValues)));

        if ($hasAllSelections) {
            // Find matching product attribute
            foreach ($this->product->productAttributes as $productAttribute) {
                $attributeData = $productAttribute->attribute_data ?? [];
                $matches = true;

                foreach ($this->selectedAttributeValues as $key => $selectedValue) {
                    if (! isset($attributeData[$key])) {
                        $matches = false;
                        break;
                    }

                    $storedValue = $attributeData[$key];

                    // Check if values match (exact match or display value match)
                    // This handles cases where stored value is "1 kg" but selected value is "1"
                    $valueMatches = $storedValue === $selectedValue;

                    // Also check if the stored value contains the selected value (for weight like "1 kg" vs "1")
                    // or if selected value contains stored value
                    if (! $valueMatches) {
                        // Try to match by checking if one contains the other (for weight with units)
                        $valueMatches = str_contains($storedValue, $selectedValue) || str_contains($selectedValue, $storedValue);

                        // Also check against display values from attribute values
                        if (! $valueMatches) {
                            $attribute = $this->attributeLookupByName->get($key);
                            if ($attribute) {
                                $attributeValue = $attribute->values->first(function ($candidate) use ($selectedValue) {
                                    return $candidate->value === $selectedValue
                                        || $candidate->getRawOriginal('display_value') === $selectedValue;
                                });

                                if ($attributeValue) {
                                    // Check if stored value matches the display value or value
                                    $valueMatches = $storedValue === $attributeValue->display_value
                                        || $storedValue === $attributeValue->value
                                        || str_contains($storedValue, $attributeValue->value)
                                        || str_contains($storedValue, $attributeValue->display_value);
                                }
                            }
                        }
                    }

                    if (! $valueMatches) {
                        $matches = false;
                        break;
                    }
                }

                if ($matches && count($this->selectedAttributeValues) === count($attributeData)) {
                    $this->selectedProductAttributeId = $productAttribute->id;
                    break;
                }
            }
        }
    }

    #[Computed]
    public function getSelectedCombinationProperty()
    {
        if ($this->product && $this->product->hasAttributes() && $this->selectedProductAttributeId) {
            return \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
        }

        return null;
    }

    /**
     * Handle JSON serialization requests (prevents errors from browser dev tools or debugging)
     */
    public function toJSON(): array
    {
        return [
            'productId' => $this->productId,
            'quantity' => $this->quantity,
            'cart' => $this->cart,
            'showCart' => $this->showCart,
            'showCheckout' => $this->showCheckout,
            'appliedCouponId' => $this->appliedCouponId,
        ];
    }

    #[Computed]
    public function getHeroSectionProperty(): ?LandingPageSection
    {
        // Always use the default hero section - the product will be displayed within it
        return Cache::remember(Tenancy::cacheKey('landing.sections.hero'), 3600, function () {
            return LandingPageSection::where('type', 'hero')
                ->where('is_active', true)
                ->orderBy('order')
                ->first();
        });
    }

    /**
     * The ordered, toggleable blocks that make up the page body (everything
     * after the pinned hero). See LandingPageConfig::normalizedBlocks() for
     * the legacy-config fallback that keeps pre-builder pages unchanged.
     */
    #[Computed]
    public function getPageBlocksProperty(): array
    {
        return $this->landingPageConfig
            ? $this->landingPageConfig->normalizedBlocks()
            : LandingPageConfig::defaultBlocks();
    }

    /**
     * LandingPageSection rows for a content-backed block (features, faq,
     * about, benefits, contact, products). Uses the block's own
     * `section_ids` when set, otherwise falls back to all active sections
     * of that type — same pattern the old per-type getters used.
     */
    public function sectionsForBlock(array $block)
    {
        $type = $block['type'];
        $sectionIds = $block['section_ids'] ?? [];

        if (! empty($sectionIds)) {
            return Cache::remember(Tenancy::cacheKey('landing.sections.'.$type.'.'.md5(implode(',', $sectionIds))), 3600, function () use ($sectionIds) {
                return LandingPageSection::whereIn('id', $sectionIds)
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });
        }

        return Cache::remember(Tenancy::cacheKey('landing.sections.'.$type), 3600, function () use ($type) {
            return LandingPageSection::where('type', $type)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    #[Computed]
    public function getTestimonialsProperty()
    {
        $testimonialsBlock = collect($this->pageBlocks)->firstWhere('type', 'testimonials');
        $testimonialIds = $testimonialsBlock['testimonial_ids'] ?? [];

        if (! empty($testimonialIds)) {
            return Cache::remember(Tenancy::cacheKey('testimonials.custom.'.md5(implode(',', $testimonialIds))), 3600, function () use ($testimonialIds) {
                return Testimonial::whereIn('id', $testimonialIds)
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });
        }

        return Cache::remember(Tenancy::cacheKey('testimonials.active'), 3600, function () {
            return Testimonial::where('is_active', true)
                ->orderBy('order')
                ->limit(6)
                ->get();
        });
    }

    #[Computed]
    public function getSiteNameProperty(): string
    {
        return Setting::get('site_name', config('app.name'));
    }

    #[Computed]
    public function getContactEmailProperty(): string
    {
        return Setting::get('contact_email', 'info@example.com');
    }

    #[Computed]
    public function getContactPhoneProperty(): string
    {
        return Setting::get('contact_phone', '+880 XXXX-XXXXXX');
    }

    #[Computed]
    public function getContactAddressProperty(): string
    {
        return Setting::get('contact_address', __('Dhaka, Bangladesh'));
    }

    #[Computed]
    public function getSocialFacebookProperty(): string
    {
        return Setting::get('social_facebook', '#');
    }

    #[Computed]
    public function getSocialInstagramProperty(): string
    {
        return Setting::get('social_instagram', '#');
    }

    #[Computed]
    public function getSocialTwitterProperty(): string
    {
        return Setting::get('social_twitter', '#');
    }

    #[Computed]
    public function getFacebookPixelIdProperty(): ?string
    {
        return Setting::get('facebook_pixel_id');
    }

    #[Computed]
    public function getSiteLogoProperty(): ?string
    {
        return Setting::get('site_logo');
    }

    public function incrementQuantity(): void
    {
        $maxStock = $this->getMaxStock();
        $this->quantity = min($maxStock, $this->quantity + 1);
    }

    private function getMaxStock(): int
    {
        if ($this->selectedCombination) {
            return $this->selectedCombination->stock;
        }

        if ($this->product) {
            return $this->product->stock;
        }

        return 0;
    }

    public function decrementQuantity(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    /**
     * Validates the current attribute/quantity selection and returns the
     * cart key, price, and attribute label to use when adding this product
     * to the cart. Returns null (after flashing an error) if invalid.
     */
    private function resolveCartEntry(): ?array
    {
        $product = $this->product;

        if (! $product) {
            session()->flash('error', __('Product not found'));

            return null;
        }

        // Priority: Check new attribute system first
        if ($product->hasAttributes()) {
            if (empty($this->selectedAttributeValues) || ! $this->selectedProductAttributeId) {
                session()->flash('error', __('Please select all required attributes'));

                return null;
            }

            $productAttribute = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);

            if (! $productAttribute || ! $productAttribute->is_active) {
                session()->flash('error', __('Selected attribute combination is not available'));

                return null;
            }

            if ($productAttribute->stock < $this->quantity) {
                session()->flash('error', __('Insufficient stock for selected attributes'));

                return null;
            }

            $price = $productAttribute->price;
            $cartKey = $product->id.'_attr_'.$productAttribute->id;
            $attributeLabel = ' ('.$productAttribute->attribute_label.')';
        } else {
            if (! $product->isInStock() || $product->stock < $this->quantity) {
                session()->flash('error', __('Product is out of stock'));

                return null;
            }

            $price = $product->price;
            $cartKey = $product->id;
            $attributeLabel = '';
        }

        return [
            'product' => $product,
            'cartKey' => $cartKey,
            'price' => $price,
            'attributeLabel' => $attributeLabel,
        ];
    }

    public function addToCart(): void
    {
        $entry = $this->resolveCartEntry();

        if (! $entry) {
            return;
        }

        ['product' => $product, 'cartKey' => $cartKey, 'price' => $price, 'attributeLabel' => $attributeLabel] = $entry;

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $this->quantity;
        } else {
            $cart[$cartKey] = [
                'id' => $product->id,
                'name' => $product->name.$attributeLabel,
                'price' => $price,
                'image' => $product->primary_image,
                'quantity' => $this->quantity,
                'attribute_data' => $this->selectedAttributeValues,
                'product_attribute_id' => $this->selectedProductAttributeId,
            ];
        }

        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->dispatch('cart-updated', count: $this->cartQuantityTotal($cart));

        $this->dispatch('fbq:track', 'AddToCart', [
            'content_type' => 'product',
            'content_ids' => [$product->id],
            'value' => $price * $this->quantity,
            'currency' => \App\Models\Setting::get('currency_code', 'BDT'),
        ]);
    }

    public function buyNow(): void
    {
        $entry = $this->resolveCartEntry();

        if (! $entry) {
            return;
        }

        ['product' => $product, 'cartKey' => $cartKey, 'price' => $price, 'attributeLabel' => $attributeLabel] = $entry;

        // Add product directly to cart for checkout
        $cart = [
            $cartKey => [
                'id' => $product->id,
                'name' => $product->name.$attributeLabel,
                'price' => $price,
                'image' => $product->primary_image,
                'quantity' => $this->quantity,
                'attribute_data' => $this->selectedAttributeValues,
                'product_attribute_id' => $this->selectedProductAttributeId,
            ],
        ];

        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->showCheckout = true;

        $this->dispatch('fbq:track', 'InitiateCheckout', [
            'content_type' => 'product',
            'content_ids' => [$product->id],
            'value' => $price * $this->quantity,
            'currency' => \App\Models\Setting::get('currency_code', 'BDT'),
        ]);
    }

    public function render()
    {
        $title = $this->landingPageConfig?->meta_title
            ?? ($this->product ? $this->product->name.' - '.$this->siteName : $this->siteName);

        $metaDescription = $this->landingPageConfig?->meta_description
            ?? ($this->product ? \Illuminate\Support\Str::limit(strip_tags($this->product->description ?? ''), 160) : null);

        return view('livewire.landing-page', [
            'metaDescription' => $metaDescription,
        ])->layout('components.layouts.public', [
            'title' => $title,
            'ogType' => $this->product ? 'product' : 'website',
            'ogImage' => $this->product?->primary_image,
            'showNavigation' => true,
            'showFooter' => true,
            'showCookieConsent' => true,
        ]);
    }
}
