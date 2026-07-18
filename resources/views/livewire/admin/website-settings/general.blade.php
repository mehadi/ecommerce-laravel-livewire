<x-website-settings.layout
    :heading="__('Site Information')"
    :subheading="__('Basic information and branding for your website')"
>
    <form wire:submit="update" class="space-y-8">
        <!-- Site Information Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.globe-alt class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Site Information') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Basic information about your website') }}</flux:text>
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
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site Tagline') }}</flux:label>
                    <flux:input
                        wire:model="site_tagline"
                        type="text"
                        placeholder="{{ $platformDefaults['site_tagline'] ?? __('Premium Date Molasses - Natural Sweetener') }}"
                    />
                    <flux:description>{{ __('A short tagline that describes your website') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site Description') }}</flux:label>
                    <flux:textarea
                        wire:model="site_description"
                        placeholder="{{ $platformDefaults['site_description'] ?? __('A brief description of your website') }}"
                        rows="3"
                    />
                    <flux:description>{{ __('A detailed description of your website and what you offer') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site URL') }}</flux:label>
                    <flux:input
                        wire:model="site_url"
                        type="url"
                        placeholder="{{ $platformDefaults['site_url'] ?? config('app.url') }}"
                    />
                    <flux:description>{{ __('The canonical URL of your website (used for SEO and social sharing)') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Site Logo') }}</flux:label>

                    <div
                        x-data="{
                            isDragging: false,
                            handleDrop(e) {
                                e.preventDefault();
                                this.isDragging = false;
                                if (e.dataTransfer.files.length) {
                                    @this.upload('logo', e.dataTransfer.files[0]);
                                }
                            },
                            handleDragOver(e) {
                                e.preventDefault();
                                this.isDragging = true;
                            },
                            handleDragLeave() {
                                this.isDragging = false;
                            }
                        }"
                        @drop.prevent="handleDrop"
                        @dragover.prevent="handleDragOver"
                        @dragleave.prevent="handleDragLeave"
                        :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-neutral-300 dark:border-neutral-600'"
                        class="relative border-2 border-dashed rounded-lg p-8 text-center transition-colors hover:border-neutral-400 dark:hover:border-neutral-500"
                    >
                        @if($existing_logo || $logo)
                            <div class="space-y-4">
                                @if($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="mx-auto max-h-32 object-contain rounded">
                                @elseif($existing_logo)
                                    <img src="{{ asset('storage/'.$existing_logo) }}" alt="Current Logo" class="mx-auto max-h-32 object-contain rounded">
                                @endif
                                <div class="flex items-center justify-center gap-3">
                                    <flux:button as="label" variant="primary" size="sm">
                                        <span class="inline-flex items-center gap-1.5">
                                            <flux:icon.arrow-up-tray class="h-4 w-4 shrink-0" />
                                            <span>{{ __('Change Logo') }}</span>
                                        </span>
                                        <input type="file" wire:model="logo" accept="image/*" class="hidden">
                                    </flux:button>
                                    @if($existing_logo)
                                        <flux:button type="button" wire:click="removeLogo" variant="danger" size="sm">
                                            <span class="inline-flex items-center gap-1.5">
                                                <flux:icon.trash class="h-4 w-4 shrink-0" />
                                                <span>{{ __('Remove') }}</span>
                                            </span>
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                @if($platformDefaults['site_logo'] ?? null)
                                    <img src="{{ asset('storage/'.$platformDefaults['site_logo']) }}" alt="Platform Default Logo" class="mx-auto max-h-20 object-contain rounded opacity-75">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-500">{{ __('Currently using the platform default logo shown above') }}</p>
                                @endif
                                <div class="flex flex-col items-center justify-center">
                                    <flux:icon.cloud-arrow-up class="size-12 text-neutral-400 mb-4" />
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2">
                                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Drop your logo here') }}</span>
                                        {{ __('or click to browse') }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-500">
                                        {{ __('PNG, JPG, GIF up to 2MB') }}
                                    </p>
                                </div>
                                <flux:button as="label" variant="primary" size="sm">
                                    <span class="inline-flex items-center gap-1.5">
                                        <flux:icon.arrow-up-tray class="h-4 w-4 shrink-0" />
                                        <span>{{ __('Select Logo') }}</span>
                                    </span>
                                    <input type="file" wire:model="logo" accept="image/*" class="hidden">
                                </flux:button>
                            </div>
                        @endif
                    </div>

                    <flux:description class="mt-2">
                        {{ __('Upload your website logo. If no logo is uploaded, the site name will be displayed as text.') }}
                    </flux:description>
                    <flux:error name="logo" />
                    <div wire:loading wire:target="logo" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        {{ __('Uploading...') }}
                    </div>
                </flux:field>

                <!-- Favicon Upload -->
                <flux:field>
                    <flux:label>{{ __('Favicon') }}</flux:label>
                    <div
                        x-data="{
                            isDragging: false,
                            handleDrop(e) {
                                e.preventDefault();
                                this.isDragging = false;
                                if (e.dataTransfer.files.length) {
                                    @this.upload('favicon', e.dataTransfer.files[0]);
                                }
                            },
                            handleDragOver(e) {
                                e.preventDefault();
                                this.isDragging = true;
                            },
                            handleDragLeave() {
                                this.isDragging = false;
                            }
                        }"
                        @drop.prevent="handleDrop"
                        @dragover.prevent="handleDragOver"
                        @dragleave.prevent="handleDragLeave"
                        :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-neutral-300 dark:border-neutral-600'"
                        class="relative border-2 border-dashed rounded-lg p-6 text-center transition-colors hover:border-neutral-400 dark:hover:border-neutral-500"
                    >
                        @if($existing_favicon || $favicon)
                            <div class="space-y-3">
                                @if($favicon)
                                    <img src="{{ $favicon->temporaryUrl() }}" alt="Favicon Preview" class="mx-auto max-h-16 object-contain rounded">
                                @elseif($existing_favicon)
                                    <img src="{{ asset('storage/'.$existing_favicon) }}" alt="Current Favicon" class="mx-auto max-h-16 object-contain rounded">
                                @endif
                                <div class="flex items-center justify-center gap-3">
                                    <flux:button as="label" variant="primary" size="sm">
                                        <span class="inline-flex items-center gap-1.5">
                                            <flux:icon.arrow-up-tray class="h-3 w-3 shrink-0" />
                                            <span>{{ __('Change') }}</span>
                                        </span>
                                        <input type="file" wire:model="favicon" accept="image/x-icon,image/png" class="hidden">
                                    </flux:button>
                                    @if($existing_favicon)
                                        <flux:button type="button" wire:click="removeFavicon" variant="danger" size="sm">
                                            <span class="inline-flex items-center gap-1.5">
                                                <flux:icon.trash class="h-3 w-3 shrink-0" />
                                                <span>{{ __('Remove') }}</span>
                                            </span>
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="space-y-3">
                                <div class="flex flex-col items-center justify-center">
                                    <flux:icon.cloud-arrow-up class="size-10 text-neutral-400 mb-2" />
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">
                                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Drop favicon here') }}</span>
                                        {{ __('or click to browse') }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-500">
                                        {{ __('ICO or PNG, up to 512KB. Recommended: 32x32 or 16x16 pixels') }}
                                    </p>
                                </div>
                                <flux:button as="label" variant="primary" size="sm">
                                    <span class="inline-flex items-center gap-1.5">
                                        <flux:icon.arrow-up-tray class="h-3 w-3 shrink-0" />
                                        <span>{{ __('Select Favicon') }}</span>
                                    </span>
                                    <input type="file" wire:model="favicon" accept="image/x-icon,image/png" class="hidden">
                                </flux:button>
                            </div>
                        @endif
                    </div>
                    <flux:description class="mt-2">
                        {{ __('Upload your website favicon (the icon shown in browser tabs). Recommended size: 32x32 or 16x16 pixels.') }}
                    </flux:description>
                    <flux:error name="favicon" />
                    <div wire:loading wire:target="favicon" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        {{ __('Uploading...') }}
                    </div>
                </flux:field>
            </div>
        </div>

        <!-- Save Button -->
        <div class="sticky bottom-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between gap-4 rounded-b-xl">
            <div class="flex-1"></div>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
