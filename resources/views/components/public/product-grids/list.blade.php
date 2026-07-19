{{-- List View: always single column; $columns doesn't apply to a row layout. --}}
<div class="flex flex-col gap-3 sm:gap-4">
    @foreach($items as $product)
        <x-public.product-cards.list :product="$product" />
    @endforeach
</div>
