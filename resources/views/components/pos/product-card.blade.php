{{--
    Grid tile for the POS product browse/search area. Assumes it's rendered
    inside the Terminal Livewire component (wire:click targets its public
    addProductToCart() method directly — this component isn't meant to be
    reused outside that context).
--}}
@props(['product'])

@php
    $outOfStock = ! $product->isInStock();
    $lowStock = ! $outOfStock && $product->isLowStock();
    $isPromo = $product->hasDiscount();
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
@endphp

<div class="group relative">
    <button
        type="button"
        wire:click="addProductToCart({{ $product->id }})"
        wire:key="pos-product-{{ $product->id }}"
        @disabled($outOfStock)
        title="{{ $outOfStock ? __('Out of stock') : '' }}"
        class="flex min-h-[120px] w-full flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white text-left transition-all duration-150 hover:-translate-y-0.5 hover:border-zinc-900 hover:shadow-md active:translate-y-0 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:border-zinc-200 disabled:hover:shadow-none dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white dark:disabled:hover:border-zinc-800 {{ $outOfStock ? '' : 'cursor-pointer' }}"
    >
        @if ($outOfStock || $lowStock || $isPromo || $isNew)
            <div class="absolute left-1.5 top-1.5 z-10 flex flex-col items-start gap-1">
                @if ($outOfStock)
                    <x-pos.badge type="out-of-stock" />
                @elseif ($lowStock)
                    <x-pos.badge type="low-stock" />
                @endif
                @if ($isPromo)
                    <x-pos.badge type="promotion" />
                @endif
                @if ($isNew)
                    <x-pos.badge type="new" />
                @endif
            </div>
        @endif

        <div class="flex h-20 items-center justify-center bg-zinc-50 dark:bg-zinc-800">
            @if ($product->primary_image)
                <img src="{{ asset('storage/'.$product->primary_image) }}" alt="{{ $product->name_en }}" class="h-full w-full object-cover">
            @else
                <svg class="h-8 w-8 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            @endif
        </div>
        <div class="flex flex-1 flex-col gap-0.5 p-2.5">
            <div class="line-clamp-2 text-sm font-medium leading-snug text-zinc-900 dark:text-white">{{ $product->name_en }}</div>
            <span class="mt-auto text-sm font-semibold tabular-nums text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($product->getSyncedPrice(), 2) }}</span>
        </div>
    </button>

    <button
        type="button"
        wire:click="viewProductDetails({{ $product->id }})"
        wire:key="pos-product-view-{{ $product->id }}"
        class="absolute right-1.5 top-1.5 z-10 flex h-7 w-7 cursor-pointer items-center justify-center rounded-full bg-white/90 text-zinc-500 shadow-sm ring-1 ring-zinc-200 transition-colors duration-150 hover:bg-white hover:text-zinc-900 dark:bg-zinc-900/90 dark:text-zinc-400 dark:ring-zinc-700 dark:hover:bg-zinc-900 dark:hover:text-white"
        aria-label="{{ __('View details') }}"
        title="{{ __('View details') }}"
    >
        <flux:icon.eye class="h-4 w-4" />
    </button>
</div>
