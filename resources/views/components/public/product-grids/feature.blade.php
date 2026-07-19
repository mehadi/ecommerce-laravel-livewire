{{-- Feature Grid: first product renders large, the rest in a Classic-style grid below. --}}
@php
    $featured = $items->first();
    $rest = $items->skip(1);

    $gridColsClass = match($columns) {
        2 => 'grid-cols-1 sm:grid-cols-2',
        4 => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3',
    };
@endphp

<div class="flex flex-col gap-5 sm:gap-6">
    @if($featured)
        <x-public.product-cards.feature :product="$featured" :featured="true" wire:key="product-{{ $featured->id }}" />
    @endif

    @if($rest->isNotEmpty())
        <div class="grid {{ $gridColsClass }} gap-5 sm:gap-6">
            @foreach($rest as $product)
                <x-public.product-cards.feature :product="$product" :featured="false" wire:key="product-{{ $product->id }}" />
            @endforeach
        </div>
    @endif
</div>
