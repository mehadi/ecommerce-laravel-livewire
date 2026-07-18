<x-website-settings.layout :heading="__('Social Media')" :subheading="__('Connect your social media profiles')">
    <form wire:submit="update" class="space-y-8">
        <!-- Social Media Links Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <flux:icon.link class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Social Media Links') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Connect your social media profiles') }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <flux:field>
                    <flux:label>{{ __('Facebook URL') }}</flux:label>
                    <flux:input
                        wire:model="social_facebook"
                        type="url"
                        placeholder="{{ $platformDefaults['social_facebook'] ?? 'https://facebook.com/yourpage' }}"
                    />
                    <flux:error name="social_facebook" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Instagram URL') }}</flux:label>
                    <flux:input
                        wire:model="social_instagram"
                        type="url"
                        placeholder="{{ $platformDefaults['social_instagram'] ?? 'https://instagram.com/yourpage' }}"
                    />
                    <flux:error name="social_instagram" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Twitter/X URL') }}</flux:label>
                    <flux:input
                        wire:model="social_twitter"
                        type="url"
                        placeholder="{{ $platformDefaults['social_twitter'] ?? 'https://twitter.com/yourpage' }}"
                    />
                    <flux:error name="social_twitter" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('LinkedIn URL') }}</flux:label>
                    <flux:input
                        wire:model="social_linkedin"
                        type="url"
                        placeholder="{{ $platformDefaults['social_linkedin'] ?? 'https://linkedin.com/company/yourpage' }}"
                    />
                    <flux:error name="social_linkedin" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('YouTube URL') }}</flux:label>
                    <flux:input
                        wire:model="social_youtube"
                        type="url"
                        placeholder="{{ $platformDefaults['social_youtube'] ?? 'https://youtube.com/@yourchannel' }}"
                    />
                    <flux:error name="social_youtube" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('TikTok URL') }}</flux:label>
                    <flux:input
                        wire:model="social_tiktok"
                        type="url"
                        placeholder="{{ $platformDefaults['social_tiktok'] ?? 'https://tiktok.com/@yourusername' }}"
                    />
                    <flux:error name="social_tiktok" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Pinterest URL') }}</flux:label>
                    <flux:input
                        wire:model="social_pinterest"
                        type="url"
                        placeholder="{{ $platformDefaults['social_pinterest'] ?? 'https://pinterest.com/yourusername' }}"
                    />
                    <flux:error name="social_pinterest" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('WhatsApp Number') }}</flux:label>
                    <flux:input
                        wire:model="social_whatsapp"
                        type="text"
                        placeholder="{{ $platformDefaults['social_whatsapp'] ?? '+1234567890' }}"
                    />
                    <flux:description>{{ __('Enter your WhatsApp number with country code (e.g., +1234567890)') }}</flux:description>
                    <flux:error name="social_whatsapp" />
                </flux:field>
            </div>
        </div>

        <!-- Save Button -->
        <div class="sticky bottom-0 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-4">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap transition-colors">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
