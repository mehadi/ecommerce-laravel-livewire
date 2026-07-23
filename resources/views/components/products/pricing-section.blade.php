@props(['price', 'compareAtPrice', 'buyingPrice', 'currency' => ''])

{{-- Profit is pure arithmetic, so it's computed client-side from deferred
     entangles — no server roundtrip per keystroke. The entangled values still
     sync back to Livewire with the next request (e.g. save). --}}
<div
    x-data="{
        price: $wire.entangle('{{ $price }}'),
        buying: $wire.entangle('{{ $buyingPrice }}'),
        get profit() {
            const price = parseFloat(this.price) || 0;
            const buying = parseFloat(this.buying) || 0;
            if (buying <= 0 || price <= 0) return 0;
            return Math.round((price - buying) * 100) / 100;
        },
        get profitPercentage() {
            const buying = parseFloat(this.buying) || 0;
            if (buying <= 0) return 0;
            return Math.round((this.profit / buying) * 10000) / 100;
        },
        format(value) {
            return (value ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    }"
    class="space-y-6"
>
    <div class="grid gap-6 md:grid-cols-2">
        <flux:field>
            <flux:label>{{ __('Price') }} *</flux:label>
            <flux:input type="number" x-model="price" step="0.01" min="0" :placeholder="__('Base selling price')" />
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
            <flux:input type="number" x-model="buying" step="0.01" min="0" :placeholder="__('Cost price for profit calculation')" />
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Enter the cost price to automatically calculate profit margin.') }}</p>
            <flux:error name="{{ $buyingPrice }}" />
        </flux:field>

        <div class="flex flex-col justify-end">
            <div
                class="rounded-xl border p-4 transition-colors"
                :class="profit > 0
                    ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/30'
                    : (profit < 0
                        ? 'border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/30'
                        : 'border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/60')"
            >
                <div class="flex items-center justify-between gap-2">
                    <span
                        class="text-xs font-medium uppercase tracking-wide"
                        :class="profit > 0
                            ? 'text-emerald-700 dark:text-emerald-400'
                            : (profit < 0 ? 'text-red-700 dark:text-red-400' : 'text-zinc-500 dark:text-zinc-400')"
                    >{{ __('Profit Margin') }}</span>

                    <flux:icon.currency-dollar
                        class="size-4 shrink-0"
                        ::class="profit > 0 ? 'text-emerald-500' : (profit < 0 ? 'text-red-500' : 'text-zinc-400')"
                        aria-hidden="true"
                    />
                </div>

                <div class="mt-1.5 flex items-baseline gap-1.5" aria-live="polite">
                    <span
                        class="text-lg font-semibold"
                        :class="profit > 0
                            ? 'text-emerald-700 dark:text-emerald-400'
                            : (profit < 0 ? 'text-red-700 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100')"
                    ><span x-text="profit > 0 ? '+' : ''"></span>{{ $currency }}<span x-text="format(profit)"></span></span>
                    <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">(<span x-text="format(profitPercentage)"></span>%)</span>
                </div>

                <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200/80 dark:bg-zinc-700/80">
                    <div
                        class="h-full rounded-full transition-all"
                        :class="profit > 0 ? 'bg-emerald-500' : (profit < 0 ? 'bg-red-500' : 'bg-zinc-400')"
                        :style="`width: ${Math.max(0, Math.min(100, Math.abs(profitPercentage)))}%`"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</div>
