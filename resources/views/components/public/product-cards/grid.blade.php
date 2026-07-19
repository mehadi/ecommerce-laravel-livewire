{{-- Classic Grid: the original, unchanged product card. --}}
@props(['product'])

<x-public.product-card :product="$product" {{ $attributes }} />
