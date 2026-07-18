@props(['price', 'compareAtPrice', 'buyingPrice', 'profit', 'profitPercentage', 'currency' => ''])

<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label>{{ __('Price') }} *</flux:label>
        <flux:input type="number" wire:model.live="{{ $price }}" step="0.01" min="0" :placeholder="__('Base selling price')" />
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('The amount customers pay at checkout.') }}</p>
        <flux:error name="{{ $price }}" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Compare at Price') }}</flux:label>
        <flux:input type="number" wire:model="{{ $compareAtPrice }}" step="0.01" min="0" :placeholder="__('Show a higher MSRP or original price')" />
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Shown with a strikethrough to highlight a discount.') }}</p>
        <flux:error name="{{ $compareAtPrice }}" />
    </flux:field>
</div>

<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label>{{ __('Buying Price') }}</flux:label>
        <flux:input type="number" wire:model.live="{{ $buyingPrice }}" step="0.01" min="0" :placeholder="__('Cost price for profit calculation')" />
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Enter the cost price to automatically calculate profit margin.') }}</p>
        <flux:error name="{{ $buyingPrice }}" />
    </flux:field>

    @if($profit !== null && $profitPercentage !== null)
        @php
            $isProfit = $profit > 0;
            $isLoss = $profit < 0;
            $barWidth = max(0, min(100, abs($profitPercentage)));
        @endphp
        <div class="flex flex-col justify-end">
            <div @class([
                'rounded-xl border p-4 transition-colors',
                'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/30' => $isProfit,
                'border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/30' => $isLoss,
                'border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/60' => ! $isProfit && ! $isLoss,
            ])>
                <div class="flex items-center justify-between gap-2">
                    <span @class([
                        'text-xs font-medium uppercase tracking-wide',
                        'text-emerald-700 dark:text-emerald-400' => $isProfit,
                        'text-red-700 dark:text-red-400' => $isLoss,
                        'text-zinc-500 dark:text-zinc-400' => ! $isProfit && ! $isLoss,
                    ])>{{ __('Profit Margin') }}</span>

                    <svg @class([
                        'h-4 w-4 shrink-0',
                        'text-emerald-500' => $isProfit,
                        'text-red-500' => $isLoss,
                        'text-zinc-400' => ! $isProfit && ! $isLoss,
                    ]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <div class="mt-1.5 flex items-baseline gap-1.5">
                    <span @class([
                        'text-lg font-semibold',
                        'text-emerald-700 dark:text-emerald-400' => $isProfit,
                        'text-red-700 dark:text-red-400' => $isLoss,
                        'text-zinc-900 dark:text-zinc-100' => ! $isProfit && ! $isLoss,
                    ])>{{ $isProfit ? '+' : '' }}{{ $currency }}{{ number_format($profit, 2) }}</span>
                    <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">({{ number_format($profitPercentage, 2) }}%)</span>
                </div>

                <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200/80 dark:bg-zinc-700/80">
                    <div @class([
                        'h-full rounded-full transition-all',
                        'bg-emerald-500' => $isProfit,
                        'bg-red-500' => $isLoss,
                        'bg-zinc-400' => ! $isProfit && ! $isLoss,
                    ]) style="width: {{ min($barWidth, 100) }}%"></div>
                </div>
            </div>
        </div>
    @endif
</div>
