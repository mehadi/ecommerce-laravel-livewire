<x-website-settings.layout :heading="__('Site Verification')" :subheading="__('Verify your website ownership with search engines')">
    <form wire:submit="update" class="space-y-8">
        <!-- Site Verification Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/20">
                    <flux:icon.shield-check class="size-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Site Verification') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Verify your website ownership with search engines') }}</flux:text>
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

        <!-- Save Button -->
        <div class="sticky bottom-0 -mx-6 -mb-6 md:mb-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between gap-4 rounded-b-xl">
            @if (session()->has('message'))
                <flux:callout variant="success" class="flex-1 mb-0">{{ session('message') }}</flux:callout>
            @else
                <div class="flex-1"></div>
            @endif
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap transition-colors">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
