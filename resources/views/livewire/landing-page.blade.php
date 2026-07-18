@push('head')
    @if($metaDescription ?? null)
        <meta name="description" content="{{ $metaDescription }}" />
    @elseif($this->product)
        <meta name="description" content="{{ \Illuminate\Support\Str::limit($this->product->description ?? '', 160) }}" />
    @endif
    @if($this->product)
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="product" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:title" content="{{ $this->product->name }}" />
        <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($this->product->description ?? '', 160) }}" />
        @if($this->product->primary_image)
            <meta property="og:image" content="{{ asset('storage/'.$this->product->primary_image) }}" />
        @endif
        
        @php
            $productData = [
                '@context' => 'https://schema.org/',
                '@type' => 'Product',
                'name' => $this->product->name,
                'description' => $this->product->description ?? '',
                'image' => $this->product->primary_image ? asset('storage/'.$this->product->primary_image) : [],
            ];

            // Handle attributes or regular product
            if ($this->product->hasAttributes() && $this->product->productAttributes->isNotEmpty()) {
                $prices = $this->product->productAttributes->pluck('price')->filter()->toArray();
                $minPrice = !empty($prices) ? min($prices) : $this->product->price;
                $maxPrice = !empty($prices) ? max($prices) : $this->product->price;
                $hasStock = $this->product->productAttributes->where('stock', '>', 0)->isNotEmpty();

                $productData['offers'] = [
                    '@type' => 'AggregateOffer',
                    'priceCurrency' => 'BDT',
                    'lowPrice' => (string) $minPrice,
                    'highPrice' => (string) $maxPrice,
                    'offerCount' => (string) $this->product->productAttributes->count(),
                    'availability' => $hasStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ];
            } else {
                $productData['offers'] = [
                    '@type' => 'Offer',
                    'price' => (string) $this->product->price,
                    'priceCurrency' => 'BDT',
                    'availability' => $this->product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                ];
            }
        @endphp
        <!-- JSON-LD Structured Data -->
        <script type="application/ld+json">
        {!! json_encode($productData, JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
@endpush

<div 
    class="min-h-screen bg-zinc-100 dark:bg-zinc-950"
    x-data
    @open-cart.window="$wire.set('showCart', true)"
>
    <!-- Order Confirmation Modal -->
    @include('components.public.order-success-modal', [
        'showOrderConfirmation' => $this->showOrderConfirmation,
        'order' => $this->order
    ])

    <!-- Hero Section -->
    <x-public.hero-section
        :heroSection="$this->heroSection"
        :product="$this->product"
        :customTitle="$this->landingPageConfig?->config['hero_title'] ?? null"
        :customContent="$this->landingPageConfig?->config['hero_content'] ?? null"
        :customBadgeText="$this->landingPageConfig?->config['hero_badge_text'] ?? null"
        :socialFacebook="$this->socialFacebook"
        :socialInstagram="$this->socialInstagram"
        :socialTwitter="$this->socialTwitter"
    />

    @if($this->product)
        @if($this->shouldShowSection('trust_badges'))
            <!-- Trust Badges Section -->
            <x-public.trust-badges />
        @endif

        @if($this->shouldShowSection('featured_categories'))
            <!-- Featured Categories Section -->
            <x-public.featured-categories-section :categories="$this->featuredCategories" />
        @endif

        @if($this->shouldShowSection('featured_products'))
            <!-- Featured Products Section -->
            <x-public.featured-products-section :products="$this->featuredProducts" />
        @endif

        @if($this->shouldShowSection('product_details'))
            <!-- Product Details Section -->
            <x-public.product-details-section :product="$this->product" />
        @endif

        @if($this->shouldShowSection('features'))
            <!-- Features Section -->
            <x-public.features-section :features="$this->features" />
        @endif

        @if($this->shouldShowSection('testimonials'))
            <!-- Testimonials Section -->
            <x-public.testimonials-section :testimonials="$this->testimonials" />
        @endif

        @if($this->shouldShowSection('faq'))
            <!-- FAQ Section -->
            <x-public.faq-section :faqs="$this->faqs" />
        @endif

        @if($this->shouldShowSection('cta'))
            <!-- CTA Section -->
            <x-public.cta-section :product="$this->product" />
        @endif
    @endif

    <!-- Sticky Cart Button -->
    <x-public.sticky-cart-button :cart="$this->cart" :cartFinalTotal="$this->cartFinalTotal" />

    <!-- Cart Modal -->
    @include('components.public.cart-modal')

    <!-- Checkout Modal -->
    @include('components.public.checkout-modal', [
        'showCheckout' => $this->showCheckout,
        'cart' => $this->cart,
        'cartFinalTotal' => $this->cartFinalTotal
    ])
</div>

