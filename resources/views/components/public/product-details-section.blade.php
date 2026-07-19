{{--
    $asH1: the standalone product page (/product/{id}) has no other h1 on the
    page, so it renders the product name as the page's h1. The landing-page
    funnel already has an h1 in its hero section, so it keeps this as an h2
    to avoid two h1s on one page.
--}}
@props(['product', 'asH1' => false])

@php
    $productDetailsVariant = \App\Support\ProductDetailsVariants::resolve(\App\Models\Setting::get('storefront_product_details_variant'));
@endphp

@if($product)
    <section id="product" class="py-4 sm:py-5 scroll-mt-20">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
                @include('components.public.product-details.'.$productDetailsVariant)
            </div>
        </div>
    </section>
@endif
