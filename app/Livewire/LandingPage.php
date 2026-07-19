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

            if ($this->landingPageConfig && $this->landingPageConfig->product_id) {
                $this->productId = $this->landingPageConfig->product_id;
            } else {
                $product = Product::where('is_active', true)
                    ->where('is_featured', true)
                    ->first() ?? Product::where('is_active', true)->first();
                $this->productId = $product?->id;
            }
        } else {
            $product = Product::where('is_active', true)
                ->where('is_featured', true)
                ->first() ?? Product::where('is_active', true)->first();

            $this->productId = $product?->id;
        }
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
                            $attribute = \App\Models\Attribute::where('name', $key)->first();
                            if ($attribute) {
                                $attributeValue = $attribute->values()
                                    ->where('value', $selectedValue)
                                    ->orWhere('display_value', $selectedValue)
                                    ->first();

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

    public function getFeaturesProperty()
    {
        if ($this->landingPageConfig && isset($this->landingPageConfig->config['features_section_ids']) && ! empty($this->landingPageConfig->config['features_section_ids'])) {
            return Cache::remember(Tenancy::cacheKey('landing.sections.features.'.md5(implode(',', $this->landingPageConfig->config['features_section_ids']))), 3600, function () {
                return LandingPageSection::whereIn('id', $this->landingPageConfig->config['features_section_ids'])
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });
        }

        return Cache::remember(Tenancy::cacheKey('landing.sections.features'), 3600, function () {
            return LandingPageSection::where('type', 'features')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    public function getTestimonialsProperty()
    {
        if ($this->landingPageConfig && isset($this->landingPageConfig->config['testimonial_ids']) && ! empty($this->landingPageConfig->config['testimonial_ids'])) {
            return Cache::remember(Tenancy::cacheKey('testimonials.custom.'.md5(implode(',', $this->landingPageConfig->config['testimonial_ids']))), 3600, function () {
                return Testimonial::whereIn('id', $this->landingPageConfig->config['testimonial_ids'])
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

    public function getFaqsProperty()
    {
        if ($this->landingPageConfig && isset($this->landingPageConfig->config['faq_section_ids']) && ! empty($this->landingPageConfig->config['faq_section_ids'])) {
            return Cache::remember(Tenancy::cacheKey('landing.sections.faq.'.md5(implode(',', $this->landingPageConfig->config['faq_section_ids']))), 3600, function () {
                return LandingPageSection::whereIn('id', $this->landingPageConfig->config['faq_section_ids'])
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });
        }

        return Cache::remember(Tenancy::cacheKey('landing.sections.faq'), 3600, function () {
            return LandingPageSection::where('type', 'faq')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    public function shouldShowSection(string $section): bool
    {
        if (! $this->landingPageConfig) {
            return true; // Default: show all sections
        }

        $configKey = 'show_'.$section;

        return $this->landingPageConfig->config[$configKey] ?? true;
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
        $this->showCart = true;
        $this->dispatch('cart-updated');

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
            ?? ($this->product ? \Illuminate\Support\Str::limit($this->product->description ?? '', 160) : null);

        return view('livewire.landing-page', [
            'metaDescription' => $metaDescription,
        ])->layout('components.layouts.public', [
            'title' => $title,
            'showNavigation' => true,
            'showFooter' => true,
            'showCookieConsent' => true,
        ]);
    }
}
