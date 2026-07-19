{{-- Compact Grid: denser column mapping, one tier higher than Classic at each breakpoint. --}}
@php
    $gridColsClass = match($columns) {
        2 => 'grid-cols-2 sm:grid-cols-3',
        4 => 'grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 2xl:grid-cols-6',
        default => 'grid-cols-2 sm:grid-cols-3 xl:grid-cols-4',
    };
@endphp

<div class="grid {{ $gridColsClass }} gap-3 sm:gap-4">
    @foreach($items as $product)
        <x-public.product-cards.compact :product="$product" />
    @endforeach
</div>
