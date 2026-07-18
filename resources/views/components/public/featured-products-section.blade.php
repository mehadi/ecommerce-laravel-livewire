@props(['products'])

@if($products && $products->count() > 0)
    <section id="featured-products" class="py-4 sm:py-5" aria-label="{{ __('Featured products') }}">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 sm:mb-12">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-amber-600/10 dark:ring-amber-500/20 mb-4">
                            {{ __('Featured') }}
                        </span>
                        <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                            {{ __('Featured Products') }}
                        </h2>
                    </div>
                    <a
                        href="{{ route('shop') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors duration-200"
                    >
                        {{ __('View All') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($products as $product)
                        <x-public.product-card :product="$product" />
                    @endforeach
                </div>

                <a
                    href="{{ route('shop') }}"
                    class="sm:hidden mt-8 inline-flex items-center justify-center gap-1.5 w-full px-5 py-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.06] text-sm font-semibold text-emerald-700 dark:text-emerald-400"
                >
                    {{ __('View All Products') }}
                </a>
            </div>
        </div>
    </section>
@endif
