{{-- Classic Grid arrangement. Receives $items (a plain Collection, already
     normalized by the product-grid dispatcher) and $columns (the shopper's
     2/3/4 toolbar choice, where applicable). --}}
@php
    $gridColsClass = match($columns) {
        2 => 'grid-cols-1 sm:grid-cols-2',
        4 => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3',
    };
@endphp

<div class="grid {{ $gridColsClass }} gap-5 sm:gap-6">
    @foreach($items as $product)
        <x-public.product-cards.grid :product="$product" wire:key="product-{{ $product->id }}" />
    @endforeach
</div>
