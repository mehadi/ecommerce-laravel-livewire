{{-- Shared filter controls, included by both the desktop sidebar and the mobile drawer. --}}
<div class="space-y-8">
    {{-- Price range --}}
    <div>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-4">{{ __('Price Range') }}</h3>
        <div class="flex items-center gap-3">
            <div class="relative flex-1 min-w-0">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-zinc-400">৳</span>
                <label for="category-min-price" class="sr-only">{{ __('Minimum price') }}</label>
                <input
                    id="category-min-price"
                    type="number"
                    inputmode="numeric"
                    min="0"
                    step="1"
                    wire:model.live.debounce.500ms="minPrice"
                    placeholder="{{ $this->priceBounds['min'] }}"
                    class="w-full min-h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl pl-7 pr-3 py-2 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 tabular-nums"
                >
            </div>
            <span class="text-zinc-300 dark:text-zinc-600 flex-shrink-0" aria-hidden="true">–</span>
            <div class="relative flex-1 min-w-0">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-zinc-400">৳</span>
                <label for="category-max-price" class="sr-only">{{ __('Maximum price') }}</label>
                <input
                    id="category-max-price"
                    type="number"
                    inputmode="numeric"
                    min="0"
                    step="1"
                    wire:model.live.debounce.500ms="maxPrice"
                    placeholder="{{ $this->priceBounds['max'] }}"
                    class="w-full min-h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl pl-7 pr-3 py-2 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 tabular-nums"
                >
            </div>
        </div>
    </div>

    {{-- Availability --}}
    <div>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-4">{{ __('Availability') }}</h3>
        <button
            type="button"
            wire:click="$set('inStockOnly', {{ $inStockOnly ? 'false' : 'true' }})"
            aria-pressed="{{ $inStockOnly ? 'true' : 'false' }}"
            class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-2xl ring-1 transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ $inStockOnly ? 'bg-emerald-50 dark:bg-emerald-900/30 ring-emerald-600/10 dark:ring-emerald-500/20' : 'bg-white dark:bg-zinc-900 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2]' }}"
        >
            <span class="text-sm font-semibold {{ $inStockOnly ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-600 dark:text-zinc-300' }}">{{ __('In Stock Only') }}</span>
            <span class="relative inline-flex h-6 w-11 flex-shrink-0 items-center rounded-full transition-colors duration-200 {{ $inStockOnly ? 'bg-emerald-600' : 'bg-zinc-200 dark:bg-zinc-700' }}">
                <span class="inline-block h-4.5 w-4.5 transform rounded-full bg-white shadow-sm transition-transform duration-200 {{ $inStockOnly ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </span>
        </button>
    </div>
</div>
