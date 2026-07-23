{{--
    Shared buy-box body: title, stock badge, attribute picker / simple price,
    optional inline description, quantity stepper, and actions. Reused by the
    classic and split-sticky product-details variants, which differ only in
    where (or whether) the description renders inline — split-sticky renders
    it separately below the sticky column instead.

    Required: $product, $asH1.
    Optional: $includeDescription (bool, default false).
--}}
@php
    $includeDescription = $includeDescription ?? false;
@endphp

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

@if($includeDescription)
    @include('components.public.product-details._description', ['product' => $product, 'style' => 'default'])
@endif

@include('components.public.product-details._quantity-stepper', ['product' => $product, 'style' => 'default'])
@include('components.public.product-details._actions', ['product' => $product, 'style' => 'default'])
