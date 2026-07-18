<x-website-settings.layout :heading="__('Analytics & Tracking')" :subheading="__('Configure tracking and analytics tools')">
    <form wire:submit="update" class="space-y-8">
        <!-- Analytics & Tracking Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/20">
                    <flux:icon.chart-bar class="size-5 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Analytics & Tracking') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Configure tracking and analytics tools') }}</flux:text>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Facebook Pixel ID') }}</flux:label>
                    <flux:input
                        wire:model="facebook_pixel_id"
                        type="text"
                        placeholder="123456789012345"
                    />
                    <flux:description>{{ __('Enter your Facebook Pixel ID to track conversions and events. You can find this in your Facebook Events Manager.') }}</flux:description>
                    <flux:error name="facebook_pixel_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Google Analytics ID') }}</flux:label>
                    <flux:input
                        wire:model="google_analytics_id"
                        type="text"
                        placeholder="G-XXXXXXXXXX or UA-XXXXXX-X"
                    />
                    <flux:description>{{ __('Enter your Google Analytics 4 (G-XXXXXXXXXX) or Universal Analytics (UA-XXXXXX-X) ID') }}</flux:description>
                    <flux:error name="google_analytics_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Google Tag Manager ID') }}</flux:label>
                    <flux:input
                        wire:model="google_tag_manager_id"
                        type="text"
                        placeholder="GTM-XXXXXXX"
                    />
                    <flux:description>{{ __('Enter your Google Tag Manager container ID (format: GTM-XXXXXXX)') }}</flux:description>
                    <flux:error name="google_tag_manager_id" />
                </flux:field>
            </div>
        </div>

        <!-- Save Button -->
        <div class="sticky bottom-0 -mx-6 -mb-6 md:mb-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between gap-4 rounded-b-xl">
            <div class="flex-1"></div>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap transition-colors">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
