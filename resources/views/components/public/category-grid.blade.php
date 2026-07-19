@props([
    'cards',
    'gridColsClass' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
])

@php
    use App\Models\Setting;
    use App\Support\CategoryGridVariants;

    $variant = CategoryGridVariants::resolve(Setting::get('storefront_category_grid_variant'));

    // The categories page passes a LengthAwarePaginator of card arrays
    // (category / productCount / subcategories). Normalize once here so no
    // variant partial needs to care whether it got a paginator or collection.
    $items = $cards instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($cards->items())
        : collect($cards);
@endphp

@include('components.public.category-grids.'.$variant, ['items' => $items, 'gridColsClass' => $gridColsClass])
