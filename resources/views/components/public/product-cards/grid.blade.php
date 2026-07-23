{{-- Classic Grid: the original, unchanged product card. --}}
@props(['product', 'eager' => false])

<x-public.product-card :product="$product" :eager="$eager" {{ $attributes }} />
