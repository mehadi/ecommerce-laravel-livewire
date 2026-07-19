{{--
    Shared order-summary block: line items, shipping cost breakdown, and the
    final total. Reused by every checkout variant so the shipping-breakdown
    logic (flat/weight/city) only lives in one place.

    Optional: $style = 'default' | 'compact' | 'plain' — controls card chrome
    density so variants can visually differ without duplicating the markup.
--}}
@php
    $style = $style ?? 'default';
    $cardClass = match ($style) {
        'plain' => '',
        'compact' => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl p-5 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]',
        default => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]',
    };
    $lineClass = match ($style) {
        'plain' => 'flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-800',
        'compact' => 'flex justify-between items-center bg-white dark:bg-zinc-900 rounded-xl px-4 py-2.5 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]',
        default => 'flex justify-between items-center bg-white dark:bg-zinc-900 rounded-2xl px-5 py-3 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]',
    };
@endphp

<div class="{{ $cardClass }}">
    @if($style !== 'plain')
        <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2.5">
            <span class="flex items-center justify-center w-8 h-8 bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] rounded-full">
                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </span>
            {{ __('Order Summary') }}
        </h3>
    @else
        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('Order Summary') }}</h3>
    @endif

    <div class="space-y-2 mb-4">
        @foreach($cart as $cartKey => $item)
            <div wire:key="checkout-line-{{ $cartKey }}" class="{{ $lineClass }}">
                <div class="flex-1 min-w-0">
                    <span class="text-sm text-zinc-900 dark:text-white font-medium">{{ $item['name'] }}</span>
                    <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-2 tabular-nums">&times;{{ $item['quantity'] }}</span>
                </div>
                <span class="text-sm font-semibold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
            </div>
        @endforeach
    </div>

    @if(!empty($cart) && $this->cartWeight > 0)
        @php
            $shippingDetails = $this->shippingDetails;
        @endphp
        <div class="border-t border-zinc-900/[0.06] dark:border-white/[0.08] pt-4 pb-1">
            <div class="flex justify-between items-start mb-1">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Shipping') }}</span>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500 tabular-nums">({{ number_format($this->cartWeight, 2) }} kg)</span>
                </div>
                <span class="text-sm font-semibold text-zinc-900 dark:text-white tabular-nums" wire:loading.class="opacity-50">
                    <span wire:loading.remove wire:target="shippingCityId,cart">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->shippingCost ?? 0, 2) }}</span>
                    <span wire:loading wire:target="shippingCityId,cart" class="text-xs text-zinc-500">{{ __('Calculating...') }}</span>
                </span>
            </div>

            @if(!empty($shippingDetails))
                <div class="mt-3 pt-3 border-t border-zinc-900/[0.04] dark:border-white/[0.06] space-y-1.5">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        <span class="font-medium">{{ $shippingDetails['description'] ?? __('Shipping') }}</span>
                        @if(isset($shippingDetails['city_name']))
                            <span class="text-zinc-400 dark:text-zinc-500"> &ndash; {{ $shippingDetails['city_name'] }}</span>
                        @endif
                    </div>

                    @if($shippingDetails['type'] === 'flat')
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1 tabular-nums">
                            <span>{{ __('Flat Rate') }}:</span>
                            <span class="font-medium">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($shippingDetails['rate'] ?? 0, 2) }}</span>
                        </div>
                    @elseif($shippingDetails['type'] === 'weight' || $shippingDetails['type'] === 'city')
                        @if(isset($shippingDetails['additional_weight']))
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 space-y-0.5 tabular-nums">
                                <div class="flex items-center justify-between">
                                    <span>{{ __('Base Rate') }} (&le;{{ number_format($shippingDetails['base_weight'] ?? 0, 2) }} kg):</span>
                                    <span class="font-medium">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($shippingDetails['base_rate'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>{{ __('Additional Weight') }} ({{ number_format($shippingDetails['additional_weight'] ?? 0, 2) }} kg @ {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($shippingDetails['per_kg_rate'] ?? 0, 2) }}/kg):</span>
                                    <span class="font-medium">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($shippingDetails['additional_cost'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center justify-between tabular-nums">
                                <span>{{ __('Base Rate') }} (&le;{{ number_format($shippingDetails['base_weight'] ?? 0, 2) }} kg):</span>
                                <span class="font-medium">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($shippingDetails['base_rate'] ?? 0, 2) }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="border-t border-zinc-900/[0.06] dark:border-white/[0.08] pt-4 flex justify-between items-center">
        <span class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
        <span class="font-display text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($cartFinalTotal, 2) }}</span>
    </div>
</div>
