@props(['products'])

@if($products && $products->count() > 0)
    <section id="featured-products" class="py-4 sm:py-5" aria-label="{{ __('Featured products') }}">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
                <x-public.section-header
                    :eyebrow="__('Featured')"
                    :title="__('Featured Products')"
                    :viewAllUrl="route('shop')"
                    :viewAllContext="__('Products')"
                    color="amber"
                />

                <x-public.product-grid :products="$products" :columns="4" setting-key="storefront_featured_grid_variant" />

                <a
                    href="{{ route('shop') }}"
                    wire:navigate
                    class="sm:hidden mt-8 inline-flex items-center justify-center gap-1.5 w-full px-5 py-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.06] text-sm font-semibold text-emerald-700 dark:text-emerald-400"
                >
                    {{ __('View All Products') }}
                </a>
            </div>
        </div>
    </section>
@endif
