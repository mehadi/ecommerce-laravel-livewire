<x-website-settings.layout :heading="__('Analytics & Tracking')" :subheading="__('Configure tracking and analytics tools')">
    <form wire:submit="update" class="space-y-8">
        <!-- Analytics & Tracking Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-orange-600 dark:text-orange-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Analytics & Tracking') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Configure tracking and analytics tools') }}</flux:text>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Facebook Pixel ID') }}</flux:label>
                    <flux:input
                        wire:model="facebook_pixel_id"
                        type="text"
                        placeholder="{{ $platformDefaults['facebook_pixel_id'] ?? '123456789012345' }}"
                    />
                    <flux:description>{{ __('Enter your Facebook Pixel ID to track conversions and events. You can find this in your Facebook Events Manager.') }}</flux:description>
                    <flux:error name="facebook_pixel_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Google Analytics ID') }}</flux:label>
                    <flux:input
                        wire:model="google_analytics_id"
                        type="text"
                        placeholder="{{ $platformDefaults['google_analytics_id'] ?? 'G-XXXXXXXXXX or UA-XXXXXX-X' }}"
                    />
                    <flux:description>{{ __('Enter your Google Analytics 4 (G-XXXXXXXXXX) or Universal Analytics (UA-XXXXXX-X) ID') }}</flux:description>
                    <flux:error name="google_analytics_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Google Tag Manager ID') }}</flux:label>
                    <flux:input
                        wire:model="google_tag_manager_id"
                        type="text"
                        placeholder="{{ $platformDefaults['google_tag_manager_id'] ?? 'GTM-XXXXXXX' }}"
                    />
                    <flux:description>{{ __('Enter your Google Tag Manager container ID (format: GTM-XXXXXXX)') }}</flux:description>
                    <flux:error name="google_tag_manager_id" />
                </flux:field>
            </div>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
