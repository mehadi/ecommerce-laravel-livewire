@php
    use App\Models\Setting;

    $checkoutIsPanel = Setting::get('checkout_display_mode', 'modal') === 'panel';
@endphp

@if($showCheckout ?? false)
    <div
        x-data="{ show: @entangle('showCheckout') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 flex {{ $checkoutIsPanel ? 'justify-end' : 'items-center justify-center p-3 sm:p-4' }}"
        @click.self="$wire.set('showCheckout', false)"
        @keydown.escape.window="$wire.set('showCheckout', false)"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Checkout') }}"
    >
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="{{ $checkoutIsPanel ? 'translate-x-full' : 'opacity-0 scale-95 translate-y-4' }}"
            x-transition:enter-end="{{ $checkoutIsPanel ? 'translate-x-0' : 'opacity-100 scale-100 translate-y-0' }}"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="{{ $checkoutIsPanel ? 'translate-x-0' : 'opacity-100 scale-100 translate-y-0' }}"
            x-transition:leave-end="{{ $checkoutIsPanel ? 'translate-x-full' : 'opacity-0 scale-95 translate-y-4' }}"
            class="bg-white dark:bg-zinc-900 flex flex-col shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden {{ $checkoutIsPanel ? 'h-full w-full sm:max-w-lg' : 'rounded-3xl max-w-2xl w-full max-h-[92vh]' }}"
        >
            <!-- Custom Scrollbar Container -->
            <style>
                .checkout-scroll::-webkit-scrollbar {
                    width: 8px;
                }
                .checkout-scroll::-webkit-scrollbar-track {
                    background: rgba(0, 0, 0, 0.04);
                    border-radius: 10px;
                }
                .checkout-scroll::-webkit-scrollbar-thumb {
                    background: #10b981;
                    border-radius: 10px;
                }
                .checkout-scroll::-webkit-scrollbar-thumb:hover {
                    background: #059669;
                }
                .dark .checkout-scroll::-webkit-scrollbar-track {
                    background: rgba(255, 255, 255, 0.05);
                }
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: rgba(0, 0, 0, 0.04);
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #a1a1aa;
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #71717a;
                }
                .dark .custom-scrollbar::-webkit-scrollbar-track {
                    background: rgba(255, 255, 255, 0.05);
                }
            </style>

            <!-- Header -->
            <div class="sticky top-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-900/[0.04] dark:border-white/[0.06] px-6 sm:px-8 py-4 sm:py-5 flex justify-between items-center z-20">
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 rounded-full">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </span>
                    {{ __('Checkout') }}
                </h2>
                <button
                    wire:click="$set('showCheckout', false)"
                    class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                    aria-label="{{ __('Close') }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="checkout-scroll overflow-y-auto flex-1" style="scrollbar-width: thin;">
                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Order Summary -->
                    @if(!empty($cart))
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                            <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2.5">
                                <span class="flex items-center justify-center w-8 h-8 bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] rounded-full">
                                    <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </span>
                                {{ __('Order Summary') }}
                            </h3>
                            <div class="space-y-2 mb-4">
                                @foreach($cart as $item)
                                    <div class="flex justify-between items-center bg-white dark:bg-zinc-900 rounded-2xl px-5 py-3 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm text-zinc-900 dark:text-white font-medium">{{ $item['name'] }}</span>
                                            <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-2 tabular-nums">&times;{{ $item['quantity'] }}</span>
                                        </div>
                                        <span class="text-sm font-semibold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Shipping Cost -->
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
                    @endif

                    <!-- Customer Details Form -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                        <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-5 flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] rounded-full">
                                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            {{ __('Your Information') }}
                        </h3>

                        <form wire:submit.prevent="placeOrder" class="space-y-5">
                            <div>
                                <label for="checkout-name" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
                                    {{ __('Full Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="checkout-name"
                                    type="text"
                                    wire:model.blur="customerName"
                                    autocomplete="name"
                                    class="w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200"
                                    x-bind:class="$wire.customerName ? 'border-emerald-400 dark:border-emerald-500' : ''"
                                    placeholder="{{ __('Enter your full name') }}"
                                    required
                                >
                                @error('customerName')
                                    <span class="text-red-600 dark:text-red-400 text-sm mt-1.5 flex items-center gap-1.5" role="alert">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="checkout-email" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
                                        {{ __('Email') }} <span class="text-zinc-400 dark:text-zinc-500 text-xs font-normal">({{ __('Optional') }})</span>
                                    </label>
                                    <input
                                        id="checkout-email"
                                        type="email"
                                        wire:model.blur="customerEmail"
                                        autocomplete="email"
                                        class="w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200"
                                        x-bind:class="$wire.customerEmail && $wire.customerEmail.includes('@') ? 'border-emerald-400 dark:border-emerald-500' : ''"
                                        placeholder="email@example.com"
                                    >
                                    @error('customerEmail')
                                        <span class="text-red-600 dark:text-red-400 text-sm mt-1.5 flex items-center gap-1.5" role="alert">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="checkout-phone" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
                                        {{ __('Phone') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="checkout-phone"
                                        type="tel"
                                        wire:model.blur="customerPhone"
                                        autocomplete="tel"
                                        inputmode="numeric"
                                        class="w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 tabular-nums"
                                        x-bind:class="$wire.customerPhone && /^01[0-9]{9}$/.test($wire.customerPhone) ? 'border-emerald-400 dark:border-emerald-500' : ($wire.customerPhone ? 'border-red-400 dark:border-red-500' : '')"
                                        placeholder="01814444444"
                                        maxlength="11"
                                        pattern="01[0-9]{9}"
                                        required
                                    >
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5">{{ __('Format: 01814444444 (11 digits starting with 01)') }}</p>
                                    @error('customerPhone')
                                        <span class="text-red-600 dark:text-red-400 text-sm mt-1.5 flex items-center gap-1.5" role="alert">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="checkout-address" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
                                    {{ __('Shipping Address') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="checkout-address"
                                    wire:model.blur="shippingAddress"
                                    autocomplete="street-address"
                                    class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 resize-none"
                                    x-bind:class="$wire.shippingAddress ? 'border-emerald-400 dark:border-emerald-500' : ''"
                                    rows="3"
                                    placeholder="{{ __('Enter your complete address') }}"
                                    required
                                ></textarea>
                                @error('shippingAddress')
                                    <span class="text-red-600 dark:text-red-400 text-sm mt-1.5 flex items-center gap-1.5" role="alert">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    x-data="{
                                        open: false,
                                        search: '',
                                        selectedCity: null,
                                        cities: @js($this->cities->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray()),
                                        get filteredCities() {
                                            if (!this.search) return this.cities;
                                            const query = this.search.toLowerCase();
                                            return this.cities.filter(city =>
                                                city.name.toLowerCase().includes(query)
                                            );
                                        },
                                        selectCity(city) {
                                            this.selectedCity = city;
                                            $wire.set('shippingCityId', city.id);
                                            this.open = false;
                                            this.search = '';
                                            // Trigger shipping cost recalculation
                                            $wire.$refresh();
                                        },
                                        init() {
                                            const selected = this.cities.find(c => c.id === $wire.shippingCityId);
                                            if (selected) this.selectedCity = selected;
                                            $watch('$wire.shippingCityId', value => {
                                                const city = this.cities.find(c => c.id === value);
                                                if (city) this.selectedCity = city;
                                            });
                                        }
                                    }"
                                    class="relative"
                                    @click.away="open = false"
                                >
                                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
                                        {{ __('City') }} <span class="text-red-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        :aria-expanded="open ? 'true' : 'false'"
                                        class="w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3 text-[15px] font-medium text-left flex items-center justify-between gap-2 cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-600 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200"
                                        :class="selectedCity ? 'border-emerald-400 dark:border-emerald-500' : ''"
                                    >
                                        <span class="truncate" :class="selectedCity ? 'text-zinc-900 dark:text-white' : 'text-zinc-400 dark:text-zinc-500'">
                                            <span x-text="selectedCity ? selectedCity.name : '{{ __('Select City') }}'"></span>
                                        </span>
                                        <svg class="w-4 h-4 text-zinc-400 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown -->
                                    <div
                                        x-show="open"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                        class="absolute z-50 w-full mt-2 bg-white dark:bg-zinc-800 rounded-2xl shadow-[0_16px_40px_-12px_rgb(16_24_40_/_0.18)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] max-h-64 overflow-hidden"
                                        style="display: none;"
                                    >
                                        <!-- Search Input -->
                                        <div class="p-2.5 border-b border-zinc-900/[0.06] dark:border-white/[0.08] sticky top-0 bg-white dark:bg-zinc-800 z-10">
                                            <div class="relative">
                                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                                <input
                                                    type="text"
                                                    x-model="search"
                                                    @click.stop
                                                    @keydown.escape="open = false"
                                                    class="w-full pl-9 pr-5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200"
                                                    placeholder="{{ __('Search cities...') }}"
                                                >
                                            </div>
                                        </div>

                                        <!-- Options List -->
                                        <div class="overflow-y-auto max-h-48 custom-scrollbar p-1">
                                            <template x-if="filteredCities.length === 0">
                                                <div class="p-4 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ __('No cities found') }}
                                                </div>
                                            </template>
                                            <template x-for="city in filteredCities" :key="city.id">
                                                <button
                                                    type="button"
                                                    @click="selectCity(city)"
                                                    class="w-full px-3.5 py-2.5 text-left text-sm rounded-xl cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors duration-150 flex items-center gap-2"
                                                    :class="selectedCity && selectedCity.id === city.id ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-semibold' : 'text-zinc-700 dark:text-zinc-300'"
                                                >
                                                    <svg x-show="selectedCity && selectedCity.id === city.id" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span x-text="city.name" class="flex-1"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    @error('shippingCityId')
                                        <span class="text-red-600 dark:text-red-400 text-sm mt-1.5 flex items-center gap-1.5" role="alert">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="checkout-postal" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">{{ __('Postal Code') }}</label>
                                    <input
                                        id="checkout-postal"
                                        type="text"
                                        wire:model.blur="shippingPostalCode"
                                        autocomplete="postal-code"
                                        inputmode="numeric"
                                        class="w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 tabular-nums"
                                        x-bind:class="$wire.shippingPostalCode ? 'border-emerald-400 dark:border-emerald-500' : ''"
                                        placeholder="{{ __('Postal Code') }}"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="checkout-notes" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">{{ __('Order Notes') }} <span class="text-zinc-400 dark:text-zinc-500 text-xs font-normal">({{ __('Optional') }})</span></label>
                                <textarea
                                    id="checkout-notes"
                                    wire:model="notes"
                                    class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 resize-none"
                                    rows="2"
                                    placeholder="{{ __('Any special instructions?') }}"
                                ></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full min-h-12 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                            >
                                <span wire:loading.remove wire:target="placeOrder" class="flex items-center gap-2.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    {{ __('Place Order') }}
                                </span>
                                <span wire:loading wire:target="placeOrder" class="flex items-center gap-2.5">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Processing...') }}
                                </span>
                            </button>

                            <!-- Security Badge -->
                            <div class="flex items-center justify-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 pt-1">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ __('Your information is secure and encrypted') }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
