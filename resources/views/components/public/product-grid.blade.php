@props([
    'products',
    'columns' => 3,
    'settingKey' => 'storefront_shop_grid_variant',
])

@php
    use App\Models\Setting;
    use App\Support\ProductGridVariants;

    $variant = ProductGridVariants::resolve(Setting::get($settingKey));

    // Shop/Category pass a LengthAwarePaginator; the homepage Featured Products
    // section passes a plain Collection. Normalize once here so no variant
    // partial needs to care which one it got.
    $items = $products instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($products->items())
        : collect($products);
@endphp

@include('components.public.product-grids.'.$variant, ['items' => $items, 'columns' => $columns])
