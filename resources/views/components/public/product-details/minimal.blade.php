{{-- Minimal product-details content — a smaller image and text-first details with plain dividers instead of cards. --}}
<div class="grid md:grid-cols-[240px_1fr] gap-8 md:gap-10 items-start">
    <div>
        @include('components.public.product-details._gallery', ['product' => $product, 'thumbLayout' => 'row'])
    </div>

    <div class="space-y-4">
        <div>
            <{{ $asH1 ? 'h1' : 'h2' }} class="font-display text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white mb-3 leading-[1.15] tracking-tight text-balance">{{ $product->name }}</{{ $asH1 ? 'h1' : 'h2' }}>
            @unless($product->hasAttributes())
                @include('components.public.product-details._stock-badge', ['product' => $product])
            @endunless
        </div>

        @if($product->hasAttributes())
            @include('components.public.product-details._attribute-picker', ['product' => $product, 'style' => 'plain'])
        @else
            @include('components.public.product-details._simple-price', ['product' => $product, 'style' => 'plain'])
        @endif

        @include('components.public.product-details._quantity-stepper', ['product' => $product, 'style' => 'plain'])
        @include('components.public.product-details._actions', ['product' => $product, 'style' => 'compact'])
        @include('components.public.product-details._description', ['product' => $product, 'style' => 'plain'])
    </div>
</div>
