<x-website-settings.layout
    :heading="__('Site Information')"
    :subheading="__('Basic information and branding for your website')"
>
    <form wire:submit="update" class="space-y-8">
        <!-- Site Information Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-blue-600 dark:text-blue-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m-15.432 0A8.959 8.959 0 0 1 3 12c0-.778.099-1.533.284-2.253m0 0A9.004 9.004 0 0 1 12 3m0 0" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Site Information') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Basic information about your website') }}</flux:text>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Site Name') }}</flux:label>
                    <flux:input
                        wire:model="site_name"
                        type="text"
                        placeholder="{{ $platformDefaults['site_name'] ?? config('app.name') }}"
                    />
                    <flux:description>
                        {{ __('The name of your website displayed throughout the site') }}
                        @if($platformDefaults['site_name'] ?? null)
                            &mdash; {{ __('leave blank to use the platform default shown above') }}
                        @endif
                    </flux:description>
                    <flux:error name="site_name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site Tagline') }}</flux:label>
                    <flux:input
                        wire:model="site_tagline"
                        type="text"
                        placeholder="{{ $platformDefaults['site_tagline'] ?? __('Premium Date Molasses - Natural Sweetener') }}"
                    />
                    <flux:description>{{ __('A short tagline that describes your website') }}</flux:description>
                    <flux:error name="site_tagline" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site Description') }}</flux:label>
                    <flux:textarea
                        wire:model="site_description"
                        placeholder="{{ $platformDefaults['site_description'] ?? __('A brief description of your website') }}"
                        rows="3"
                    />
                    <flux:description>{{ __('A detailed description of your website and what you offer') }}</flux:description>
                    <flux:error name="site_description" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site URL') }}</flux:label>
                    <flux:input
                        wire:model="site_url"
                        type="url"
                        placeholder="{{ $platformDefaults['site_url'] ?? config('app.url') }}"
                    />
                    <flux:description>{{ __('The canonical URL of your website (used for SEO and social sharing)') }}</flux:description>
                    <flux:error name="site_url" />
                </flux:field>

                <x-website-settings.file-dropzone
                    wire-model="logo"
                    :label="__('Site Logo')"
                    :current-url="$existing_logo ? asset('storage/'.$existing_logo) : null"
                    :new-file="$logo"
                    remove-action="removeLogo"
                    :remove-confirm-message="__('Are you sure you want to remove the site logo? This cannot be undone.')"
                    aspect="lg"
                    hint="{{ __('PNG, JPG, GIF up to 2MB') }}"
                >
                    <x-slot:description>
                        {{ __('Upload your website logo. If no logo is uploaded, the site name will be displayed as text.') }}
                        @if(!$existing_logo && !$logo && ($platformDefaults['site_logo'] ?? null))
                            {{ __('Currently using the platform default logo shown in your storefront until you upload your own.') }}
                        @endif
                    </x-slot:description>
                </x-website-settings.file-dropzone>

                <!-- Favicon Upload -->
                <x-website-settings.file-dropzone
                    wire-model="favicon"
                    :label="__('Favicon')"
                    :current-url="$existing_favicon ? asset('storage/'.$existing_favicon) : null"
                    :new-file="$favicon"
                    remove-action="removeFavicon"
                    :remove-confirm-message="__('Are you sure you want to remove the favicon? This cannot be undone.')"
                    aspect="square"
                    accept="image/x-icon,image/png"
                    hint="{{ __('ICO or PNG, up to 512KB. Recommended: 32x32 or 16x16 pixels') }}"
                >
                    <x-slot:description>
                        {{ __('Upload your website favicon (the icon shown in browser tabs). Recommended size: 32x32 or 16x16 pixels.') }}
                    </x-slot:description>
                </x-website-settings.file-dropzone>
            </div>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
