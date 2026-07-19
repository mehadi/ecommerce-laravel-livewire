@php
    $gridColsClass = match($columns) {
        2 => 'grid-cols-1 sm:grid-cols-2',
        4 => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3',
    };
@endphp

<div class="grid {{ $gridColsClass }} gap-x-5 sm:gap-x-6 gap-y-8 sm:gap-y-10">
    @foreach($items as $product)
        <x-public.product-cards.minimal :product="$product" wire:key="product-{{ $product->id }}" />
    @endforeach
</div>
