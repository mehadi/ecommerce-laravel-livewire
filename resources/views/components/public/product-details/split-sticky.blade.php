{{-- Split Sticky product-details content — the buy box stays pinned while the description scrolls below it. --}}
<div class="grid md:grid-cols-2 gap-10 md:gap-14 lg:gap-16 items-start">
    <div>
        @include('components.public.product-details._gallery', ['product' => $product, 'thumbLayout' => 'row'])
    </div>

    <div class="md:sticky md:top-24 space-y-5">
        @include('components.public.product-details._buy-box', ['product' => $product, 'asH1' => $asH1])
    </div>
</div>

<div class="mt-8 md:mt-10 max-w-3xl">
    @include('components.public.product-details._description', ['product' => $product, 'style' => 'default'])
</div>
