{{--
    Shared contact fields: full name, email, phone (with per-tenant format
    pattern/placeholder). Reused by every checkout variant.

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
        'plain' => 'w-full bg-transparent border-0 border-b border-zinc-300 dark:border-zinc-700 px-0 py-1.5 text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-white focus:outline-none focus:ring-0',
        'compact' => 'w-full min-h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-2 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200',
        default => 'w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-3 text-[15px] font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200',
    };
    $gapClass = $style === 'plain' ? 'space-y-4' : 'space-y-5';
@endphp

<div class="{{ $gapClass }}">
    <div>
        <label for="checkout-name" class="{{ $labelClass }}">
            {{ __('Full Name') }} <span class="text-red-500">*</span>
        </label>
        <input
            id="checkout-name"
            type="text"
            wire:model.blur="customerName"
            autocomplete="name"
            class="{{ $inputClass }}"
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
            <label for="checkout-email" class="{{ $labelClass }}">
                {{ __('Email') }} <span class="text-zinc-400 dark:text-zinc-500 text-xs font-normal">({{ __('Optional') }})</span>
            </label>
            <input
                id="checkout-email"
                type="email"
                wire:model.blur="customerEmail"
                autocomplete="email"
                class="{{ $inputClass }}"
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
            <label for="checkout-phone" class="{{ $labelClass }}">
                {{ __('Phone') }} <span class="text-red-500">*</span>
            </label>
            <input
                id="checkout-phone"
                type="tel"
                wire:model.blur="customerPhone"
                autocomplete="tel"
                inputmode="{{ $this->phoneFormatPreset === 'intl' ? 'tel' : 'numeric' }}"
                class="{{ $inputClass }} tabular-nums"
                x-bind:class="$wire.customerPhone && new RegExp('{{ $this->phonePattern }}').test($wire.customerPhone) ? 'border-emerald-400 dark:border-emerald-500' : ($wire.customerPhone ? 'border-red-400 dark:border-red-500' : '')"
                placeholder="{{ $this->phonePlaceholder }}"
                pattern="{{ trim($this->phonePattern, '^$') }}"
                required
            >
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5">{{ __('Format: :example', ['example' => $this->phonePlaceholder]) }}</p>
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
</div>
