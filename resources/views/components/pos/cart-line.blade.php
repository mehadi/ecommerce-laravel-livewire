@props(['lineKey', 'line'])

<div wire:key="cart-{{ $lineKey }}" class="flex items-center gap-2 border-b border-zinc-100 py-2.5 last:border-0 dark:border-zinc-800">
    <div class="min-w-0 flex-1">
        <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $line['product_name'] }}</div>
        @if ($line['attribute_data'])
            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ collect($line['attribute_data'])->map(fn ($v, $k) => "$k: $v")->join(', ') }}</div>
        @endif
        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($line['unit_price'], 2) }} {{ __('each') }}</div>
    </div>

    <div class="flex items-center rounded-lg border border-zinc-200 dark:border-zinc-700">
        <button type="button" wire:click="decrementCartQuantity('{{ $lineKey }}')" class="flex h-9 w-9 cursor-pointer items-center justify-center text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800" aria-label="{{ __('Decrease quantity') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
        </button>
        <span class="w-8 text-center text-sm font-medium tabular-nums">{{ $line['quantity'] }}</span>
        <button type="button" wire:click="incrementCartQuantity('{{ $lineKey }}')" class="flex h-9 w-9 cursor-pointer items-center justify-center text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800" aria-label="{{ __('Increase quantity') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>
    </div>

    <div class="w-20 text-right text-sm font-semibold tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($line['unit_price'] * $line['quantity'], 2) }}</div>

    @can('void pos sale line')
        <button type="button" wire:click="removeCartLine('{{ $lineKey }}')" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-zinc-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40" aria-label="{{ __('Remove') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    @endcan
</div>
