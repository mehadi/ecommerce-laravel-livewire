@props(['price', 'compareAtPrice', 'buyingPrice', 'profit', 'profitPercentage'])

<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label>{{ __('Price') }} *</flux:label>
        <flux:input type="number" wire:model.live="{{ $price }}" step="0.01" min="0" placeholder="{{ __('Base selling price') }}" />
        <flux:error name="{{ $price }}" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Compare at Price') }}</flux:label>
        <flux:input type="number" wire:model="{{ $compareAtPrice }}" step="0.01" min="0" placeholder="{{ __('Show a higher MSRP or original price') }}" />
        <flux:error name="{{ $compareAtPrice }}" />
    </flux:field>
</div>

<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label>{{ __('Buying Price') }}</flux:label>
        <flux:input type="number" wire:model.live="{{ $buyingPrice }}" step="0.01" min="0" placeholder="{{ __('Cost price for profit calculation') }}" />
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Enter the cost price to automatically calculate profit margin.') }}</p>
        <flux:error name="{{ $buyingPrice }}" />
    </flux:field>

    @if($profit !== null && $profitPercentage !== null)
        <div class="flex flex-col justify-end">
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Profit Calculation') }}</div>
                <div class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($profit, 2) }} <span class="text-sm font-normal text-zinc-600 dark:text-zinc-400">({{ number_format($profitPercentage, 2) }}%)</span>
                </div>
            </div>
        </div>
    @endif
</div>

