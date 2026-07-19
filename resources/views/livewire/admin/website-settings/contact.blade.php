<x-website-settings.layout :heading="__('Contact Information')" :subheading="__('Contact details displayed on your website')">
    <form wire:submit="update" class="space-y-8">
        <!-- Contact Information Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-emerald-600 dark:text-emerald-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Contact Information') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Contact details displayed on your website') }}</flux:text>
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

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
