<x-website-settings.layout :heading="__('Site Verification')" :subheading="__('Verify your website ownership with search engines')">
    <form wire:submit="update" class="space-y-8">
        <!-- Site Verification Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-indigo-600 dark:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Site Verification') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Verify your website ownership with search engines') }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <flux:field>
                    <flux:label>{{ __('Google Search Console Verification Code') }}</flux:label>
                    <flux:input
                        wire:model="google_verification_code"
                        type="text"
                        placeholder="{{ $platformDefaults['google_verification_code'] ?? __('Enter verification code') }}"
                    />
                    <flux:description>{{ __('Enter the meta tag content value from Google Search Console') }}</flux:description>
                    <flux:error name="google_verification_code" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Bing Webmaster Tools Verification Code') }}</flux:label>
                    <flux:input
                        wire:model="bing_verification_code"
                        type="text"
                        placeholder="{{ $platformDefaults['bing_verification_code'] ?? __('Enter verification code') }}"
                    />
                    <flux:description>{{ __('Enter the meta tag content value from Bing Webmaster Tools') }}</flux:description>
                    <flux:error name="bing_verification_code" />
                </flux:field>
            </div>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
