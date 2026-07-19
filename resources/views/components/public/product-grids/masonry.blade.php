{{-- Masonry Grid: CSS multi-column layout; cards use natural image height so
     column heights vary. break-inside-avoid keeps each card from splitting
     across columns. --}}
@php
    $columnsClass = match($columns) {
        2 => 'columns-1 sm:columns-2',
        4 => 'columns-1 sm:columns-2 xl:columns-3 2xl:columns-4',
        default => 'columns-1 sm:columns-2 xl:columns-3',
    };
@endphp

<div class="{{ $columnsClass }} gap-5 sm:gap-6 [column-fill:balance]">
    @foreach($items as $product)
        <div class="break-inside-avoid mb-5 sm:mb-6">
            <x-public.product-cards.masonry :product="$product" />
        </div>
    @endforeach
</div>
