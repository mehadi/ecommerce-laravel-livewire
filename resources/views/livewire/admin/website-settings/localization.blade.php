<x-website-settings.layout :heading="__('Localization')" :subheading="__('Currency and phone number format for your store')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <flux:icon.banknotes class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Currency') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Shown on product prices and checkout') }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <flux:field>
                    <flux:label>{{ __('Currency Code') }}</flux:label>
                    <flux:input
                        wire:model="currency_code"
                        type="text"
                        placeholder="BDT"
                    />
                    <flux:description>{{ __('ISO currency code, e.g. BDT, INR, USD') }}</flux:description>
                    <flux:error name="currency_code" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Currency Symbol') }}</flux:label>
                    <flux:input
                        wire:model="currency_symbol"
                        type="text"
                        placeholder="৳"
                    />
                    <flux:description>{{ __('Symbol displayed next to prices, e.g. ৳, ₹, $') }}</flux:description>
                    <flux:error name="currency_symbol" />
                </flux:field>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/20">
                    <flux:icon.phone class="size-5 text-sky-600 dark:text-sky-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Phone Number Format') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Validation used at checkout for customer phone numbers') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Format') }}</flux:label>
                <flux:select wire:model="phone_format_preset">
                    @foreach ($phonePresets as $key => $preset)
                        <option value="{{ $key }}">{{ $preset['label'] }}</option>
                    @endforeach
                </flux:select>
                <flux:description>{{ __('Choose the format matching your customers\' phone numbers') }}</flux:description>
                <flux:error name="phone_format_preset" />
            </flux:field>
        </div>

        <div class="sticky bottom-0 -mx-6 -mb-6 md:mb-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-4 rounded-b-xl">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap transition-colors">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
