{{-- Compact row alternative to x-pos.product-card, for the list density toggle. --}}
@props(['product'])

@php
    $outOfStock = ! $product->isInStock();
    $lowStock = ! $outOfStock && $product->isLowStock();
    $isPromo = $product->hasDiscount();
@endphp

<div class="flex min-h-[56px] w-full items-center border-b border-zinc-100 last:border-0 dark:border-zinc-800">
    <button
        type="button"
        wire:click="addProductToCart({{ $product->id }})"
        wire:key="pos-product-row-{{ $product->id }}"
        @disabled($outOfStock)
        class="flex min-w-0 flex-1 items-center gap-3 px-3 py-2 text-left transition-colors duration-150 hover:bg-zinc-50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent dark:hover:bg-zinc-800 {{ $outOfStock ? '' : 'cursor-pointer' }}"
    >
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
            @if ($product->primary_image)
                <img src="{{ asset('storage/'.$product->primary_image) }}" alt="{{ $product->name_en }}" class="h-full w-full rounded-lg object-cover">
            @else
                <svg class="h-5 w-5 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $product->name_en }}</div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $product->sku ?? '—' }}</span>
                @if ($outOfStock)
                    <x-pos.badge type="out-of-stock" />
                @elseif ($lowStock)
                    <x-pos.badge type="low-stock" />
                @endif
                @if ($isPromo)
                    <x-pos.badge type="promotion" />
                @endif
            </div>
        </div>
        <span class="shrink-0 text-sm font-semibold tabular-nums text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($product->getSyncedPrice(), 2) }}</span>
    </button>

    <button
        type="button"
        wire:click="viewProductDetails({{ $product->id }})"
        wire:key="pos-product-row-view-{{ $product->id }}"
        class="flex h-full w-11 shrink-0 cursor-pointer items-center justify-center border-l border-zinc-100 text-zinc-400 transition-colors duration-150 hover:bg-zinc-50 hover:text-zinc-900 dark:border-zinc-800 dark:hover:bg-zinc-800 dark:hover:text-white"
        aria-label="{{ __('View details') }}"
        title="{{ __('View details') }}"
    >
        <flux:icon.eye class="h-4 w-4" />
    </button>
</div>
