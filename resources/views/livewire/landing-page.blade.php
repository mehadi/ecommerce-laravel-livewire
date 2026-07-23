@push('head')
    @if($this->product)
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
                    'priceCurrency' => \App\Models\Setting::get('currency_code', 'BDT'),
                    'lowPrice' => (string) $minPrice,
                    'highPrice' => (string) $maxPrice,
                    'offerCount' => (string) $this->product->productAttributes->count(),
                    'availability' => $hasStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ];
            } else {
                $productData['offers'] = [
                    '@type' => 'Offer',
                    'price' => (string) $this->product->price,
                    'priceCurrency' => \App\Models\Setting::get('currency_code', 'BDT'),
                    'availability' => $this->product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                ];
            }
        @endphp
        <!-- JSON-LD Structured Data -->
        <script type="application/ld+json">
        {!! json_encode($productData, JSON_UNESCAPED_SLASHES) !!}
        </script>

        @php
            $breadcrumbSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $this->product->name, 'item' => url()->current()],
                ],
            ];
        @endphp
        {{-- JSON_HEX_TAG so an admin-authored product name containing "</script>" can't break out of this script context. --}}
        <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
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
        <nav aria-label="{{ __('Breadcrumb') }}" class="container mx-auto px-4 sm:px-6 frontend-container pt-4 sm:pt-5">
            <ol class="flex flex-wrap items-center gap-2 text-sm">
                <li>
                    <a href="{{ route('home') }}" wire:navigate class="font-medium text-zinc-500 dark:text-zinc-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-lg">
                        {{ __('Home') }}
                    </a>
                </li>
                <li aria-hidden="true">
                    <svg class="w-3.5 h-3.5 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </li>
                <li>
                    <span class="font-semibold text-zinc-900 dark:text-white" aria-current="page">{{ $this->product->name }}</span>
                </li>
            </ol>
        </nav>

        @foreach($this->pageBlocks as $block)
            @continue(! ($block['enabled'] ?? true))
            @switch($block['type'])
                @case('trust_badges')
                    <x-public.trust-badges />
                    @break

                @case('product_details')
                    <x-public.product-details-section :product="$this->product" />
                    @break

                @case('features')
                    <x-public.features-section :features="$this->sectionsForBlock($block)" />
                    @break

                @case('testimonials')
                    <x-public.testimonials-section :testimonials="$this->testimonials" />
                    @break

                @case('faq')
                    <x-public.faq-section :faqs="$this->sectionsForBlock($block)" />
                    @break

                @case('cta')
                    <x-public.cta-section :product="$this->product" />
                    @break

                @case('about')
                @case('benefits')
                @case('contact')
                @case('products')
                    <x-public.content-section :sections="$this->sectionsForBlock($block)" :heading="$block['type']" />
                    @break
            @endswitch
        @endforeach
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

