@push('head')
    @if($metaDescription ?? null)
        <meta name="description" content="{{ $metaDescription }}" />
    @endif
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $this->product->name }}" />
    @if($metaDescription ?? null)
        <meta property="og:description" content="{{ $metaDescription }}" />
    @endif
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

    <!-- Breadcrumb -->
    <section class="pt-2 sm:pt-3 pb-4 sm:pb-5">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] px-6 sm:px-10 lg:px-14 pb-5 sm:pb-6 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[6.75rem] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
                <nav aria-label="{{ __('Breadcrumb') }}">
                    <ol class="flex flex-wrap items-center gap-2 text-sm">
                        <li>
                            <a href="{{ route('shop') }}" wire:navigate class="font-medium text-zinc-500 dark:text-zinc-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-lg">
                                {{ __('Shop') }}
                            </a>
                        </li>
                        @if($this->product->category)
                            <li aria-hidden="true">
                                <svg class="w-3.5 h-3.5 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </li>
                            <li>
                                <a href="{{ route('shop', ['category' => $this->product->category_id]) }}" wire:navigate class="font-medium text-zinc-500 dark:text-zinc-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-lg">
                                    {{ $this->product->category->name }}
                                </a>
                            </li>
                        @endif
                        <li aria-hidden="true">
                            <svg class="w-3.5 h-3.5 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                        </li>
                        <li>
                            <span class="font-semibold text-zinc-900 dark:text-white" aria-current="page">{{ $this->product->name }}</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- Product Details Section -->
    <x-public.product-details-section :product="$this->product" />

    <!-- Related Products -->
    @if($this->relatedProducts->count() > 0)
        <section class="py-4 sm:py-5" aria-labelledby="related-heading">
            <div class="container mx-auto px-4 sm:px-6 frontend-container">
                <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 sm:mb-12">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                                {{ __('Keep Browsing') }}
                            </span>
                            <h2 id="related-heading" class="font-display text-2xl sm:text-3xl font-bold leading-tight text-zinc-900 dark:text-white tracking-tight text-balance">
                                {{ __('You May Also Like') }}
                            </h2>
                        </div>
                        <a href="{{ route('shop') }}" wire:navigate class="inline-flex items-center gap-2 self-start sm:self-auto min-h-10 px-5 py-2 rounded-full text-sm font-semibold bg-zinc-50 dark:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                            {{ __('View All Products') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                        @foreach($this->relatedProducts as $related)
                            <x-public.product-card :product="$related" />
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
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
