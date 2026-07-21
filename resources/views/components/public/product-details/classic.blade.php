{{-- Classic product-details content — sticky image with thumbnails on the left, details on the right. --}}
<div class="grid md:grid-cols-2 gap-10 md:gap-14 lg:gap-16 items-start">
    <div class="md:sticky md:top-24">
        @include('components.public.product-details._gallery', ['product' => $product, 'thumbLayout' => 'row'])
    </div>

    <div class="space-y-5">
        <div>
            <{{ $asH1 ? 'h1' : 'h2' }} class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white mb-4 leading-[1.1] tracking-tight text-balance">{{ $product->name }}</{{ $asH1 ? 'h1' : 'h2' }}>
            @unless($product->hasAttributes())
                @include('components.public.product-details._stock-badge', ['product' => $product])
            @endunless
        </div>

        @if($product->hasAttributes())
            @include('components.public.product-details._attribute-picker', ['product' => $product, 'style' => 'default'])
        @else
            @include('components.public.product-details._simple-price', ['product' => $product, 'style' => 'default'])
        @endif

        @include('components.public.product-details._description', ['product' => $product, 'style' => 'default'])
        @include('components.public.product-details._quantity-stepper', ['product' => $product, 'style' => 'default'])
        @include('components.public.product-details._actions', ['product' => $product, 'style' => 'default'])
    </div>
</div>
