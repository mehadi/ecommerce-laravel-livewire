{{-- Classic product-details content — sticky image with thumbnails on the left, details on the right. --}}
<div class="grid md:grid-cols-2 gap-10 md:gap-14 lg:gap-16 items-start">
    <div class="md:sticky md:top-24">
        @include('components.public.product-details._gallery', ['product' => $product, 'thumbLayout' => 'row'])
    </div>

    <div class="space-y-5">
        @include('components.public.product-details._buy-box', ['product' => $product, 'asH1' => $asH1, 'includeDescription' => true])
    </div>
</div>
