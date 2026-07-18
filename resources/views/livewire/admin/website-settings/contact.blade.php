<x-website-settings.layout :heading="__('Contact Information')" :subheading="__('Contact details displayed on your website')">
    <form wire:submit="update" class="space-y-8">
        <!-- Contact Information Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/20">
                    <flux:icon.envelope class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Contact Information') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Contact details displayed on your website') }}</flux:text>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Contact Email') }}</flux:label>
                    <flux:input
                        wire:model="contact_email"
                        type="email"
                        placeholder="{{ $platformDefaults['contact_email'] ?? 'info@example.com' }}"
                    />
                    <flux:description>{{ __('Email address shown in the contact section') }}</flux:description>
                    <flux:error name="contact_email" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Contact Phone') }}</flux:label>
                    <flux:input
                        wire:model="contact_phone"
                        type="text"
                        placeholder="{{ $platformDefaults['contact_phone'] ?? '+880 XXXX-XXXXXX' }}"
                    />
                    <flux:description>{{ __('Phone number displayed for customer inquiries') }}</flux:description>
                    <flux:error name="contact_phone" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Contact Address') }}</flux:label>
                    <flux:textarea
                        wire:model="contact_address"
                        placeholder="{{ $platformDefaults['contact_address'] ?? __('Enter your business address') }}"
                        rows="2"
                    />
                    <flux:description>{{ __('Physical address or location information') }}</flux:description>
                    <flux:error name="contact_address" />
                </flux:field>
            </div>
        </div>

        <!-- Save Button -->
        <div class="sticky bottom-0 -mx-6 -mb-6 md:mb-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-4 rounded-b-xl">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap transition-colors">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
