{{--
    Shared shipping-address fields: address textarea, city (via _city-select)
    + postal code, and an optional notes textarea. Reused by every checkout
    variant.

    Optional: $style = 'default' | 'compact' | 'plain' — controls field chrome.
--}}
@php
    $style = $style ?? 'default';
    $labelClass = match ($style) {
        'plain' => 'block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1',
        'compact' => 'block text-xs font-semibold text-zinc-900 dark:text-white mb-1.5',
        default => 'block text-sm font-semibold text-zinc-900 dark:text-white mb-2',
    };
    $inputClass = match ($style) {
        'plain' => 'w-full bg-transparent border-0 border-b border-zinc-300 dark:border-zinc-700 px-0 py-1.5 text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-white focus:outline-none focus:ring-0 resize-none',
        'compact' => 'w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-2 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 resize-none',
        default => 'w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 resize-none',
    };
    $postalInputClass = match ($style) {
        'plain' => 'w-full bg-transparent border-0 border-b border-zinc-300 dark:border-zinc-700 px-0 py-1.5 text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-white focus:outline-none focus:ring-0 tabular-nums',
        'compact' => 'w-full min-h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-2 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 tabular-nums',
        default => 'w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200 tabular-nums',
    };
    $gapClass = $style === 'plain' ? 'space-y-4' : 'space-y-5';
@endphp

<div class="{{ $gapClass }}">
    <div>
        <label for="checkout-address" class="{{ $labelClass }}">
            {{ __('Shipping Address') }} <span class="text-red-500">*</span>
        </label>
        <textarea
            id="checkout-address"
            wire:model.blur="shippingAddress"
            autocomplete="street-address"
            class="{{ $inputClass }}"
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
        @include('components.public.checkouts._city-select', ['dense' => $style !== 'default'])
        <div>
            <label for="checkout-postal" class="{{ $labelClass }}">{{ __('Postal Code') }}</label>
            <input
                id="checkout-postal"
                type="text"
                wire:model.blur="shippingPostalCode"
                autocomplete="postal-code"
                inputmode="numeric"
                class="{{ $postalInputClass }}"
                x-bind:class="$wire.shippingPostalCode ? 'border-emerald-400 dark:border-emerald-500' : ''"
                placeholder="{{ __('Postal Code') }}"
            >
        </div>
    </div>

    <div>
        <label for="checkout-notes" class="{{ $labelClass }}">{{ __('Order Notes') }} <span class="text-zinc-400 dark:text-zinc-500 text-xs font-normal">({{ __('Optional') }})</span></label>
        <textarea
            id="checkout-notes"
            wire:model="notes"
            class="{{ $inputClass }}"
            rows="2"
            placeholder="{{ __('Any special instructions?') }}"
        ></textarea>
    </div>
</div>
