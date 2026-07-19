<x-website-settings.layout :heading="__('Localization')" :subheading="__('Currency and phone number format for your store')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-amber-600 dark:text-amber-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Currency') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Shown on product prices and checkout') }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Currency Code') }}</flux:label>
                    <flux:input
                        wire:model="currency_code"
                        type="text"
                        required
                        placeholder="BDT"
                    />
                    <flux:description>{{ __('ISO currency code, e.g. BDT, INR, USD') }}</flux:description>
                    <flux:error name="currency_code" />
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Currency Symbol') }}</flux:label>
                    <flux:input
                        wire:model="currency_symbol"
                        type="text"
                        required
                        placeholder="৳"
                    />
                    <flux:description>{{ __('Symbol displayed next to prices, e.g. ৳, ₹, $') }}</flux:description>
                    <flux:error name="currency_symbol" />
                </flux:field>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-sky-600 dark:text-sky-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Phone Number Format') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Validation used at checkout for customer phone numbers') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label badge="{{ __('Required') }}">{{ __('Format') }}</flux:label>
                <flux:select wire:model="phone_format_preset" required>
                    @foreach ($phonePresets as $key => $preset)
                        <option value="{{ $key }}">{{ $preset['label'] }}</option>
                    @endforeach
                </flux:select>
                <flux:description>{{ __('Choose the format matching your customers\' phone numbers') }}</flux:description>
                <flux:error name="phone_format_preset" />
            </flux:field>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
