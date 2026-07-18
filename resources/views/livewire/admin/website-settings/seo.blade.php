<x-website-settings.layout :heading="__('SEO Settings')" :subheading="__('Optimize your website for search engines')">
    <form wire:submit="update" class="space-y-6">
        <!-- SEO Settings Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <flux:icon.magnifying-glass class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('SEO Settings') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Optimize your website for search engines') }}</flux:text>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Meta Description') }}</flux:label>
                    <flux:textarea
                        wire:model.live="meta_description"
                        placeholder="{{ $platformDefaults['meta_description'] ?? __('A brief description for search engines (recommended: 150-160 characters)') }}"
                        rows="3"
                    />
                    <div class="flex items-center justify-between mt-1">
                        <flux:description>{{ __('This description appears in search engine results. Keep it between 150-160 characters for optimal display.') }}</flux:description>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium {{ $this->metaDescriptionColor }}">
                                <span wire:ignore>{{ $this->metaDescriptionLength }}</span> / 160
                            </span>
                            @if($this->metaDescriptionLength > 0 && $this->metaDescriptionLength < 120)
                                <span class="text-xs text-yellow-600 dark:text-yellow-400" title="{{ __('Too short - aim for 120-160 characters') }}">
                                    ⚠️
                                </span>
                            @elseif($this->metaDescriptionLength > 160)
                                <span class="text-xs text-red-600 dark:text-red-400" title="{{ __('Too long - may be truncated in search results') }}">
                                    ⚠️
                                </span>
                            @elseif($this->metaDescriptionLength >= 120 && $this->metaDescriptionLength <= 160)
                                <span class="text-xs text-green-600 dark:text-green-400" title="{{ __('Optimal length') }}">
                                    ✓
                                </span>
                            @endif
                        </div>
                    </div>
                    <flux:error name="meta_description" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Meta Keywords') }}</flux:label>
                    <flux:textarea
                        wire:model="meta_keywords"
                        placeholder="{{ $platformDefaults['meta_keywords'] ?? __('Comma-separated keywords for SEO') }}"
                        rows="2"
                    />
                    <flux:description>{{ __('Enter relevant keywords separated by commas to help search engines understand your content') }}</flux:description>
                    <flux:error name="meta_keywords" />
                </flux:field>

                <!-- Open Graph Image -->
                <flux:field>
                    <flux:label>{{ __('Open Graph Image') }}</flux:label>
                    <div
                        x-data="{
                            isDragging: false,
                            handleDrop(e) {
                                e.preventDefault();
                                this.isDragging = false;
                                if (e.dataTransfer.files.length) {
                                    @this.upload('og_image', e.dataTransfer.files[0]);
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
                        @if($existing_og_image || $og_image)
                            <div class="space-y-3">
                                @if($og_image)
                                    <img src="{{ $og_image->temporaryUrl() }}" alt="OG Image Preview" class="mx-auto max-h-32 object-contain rounded">
                                @elseif($existing_og_image)
                                    <img src="{{ asset('storage/'.$existing_og_image) }}" alt="Current OG Image" class="mx-auto max-h-32 object-contain rounded">
                                @endif
                                <div class="flex items-center justify-center gap-3">
                                    <flux:button as="label" variant="primary" size="sm">
                                        <span class="inline-flex items-center gap-1.5">
                                            <flux:icon.arrow-up-tray class="h-3 w-3 shrink-0" />
                                            <span>{{ __('Change') }}</span>
                                        </span>
                                        <input type="file" wire:model="og_image" accept="image/*" class="hidden">
                                    </flux:button>
                                    @if($existing_og_image)
                                        <flux:button type="button" wire:click="removeOgImage" variant="danger" size="sm">
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
                                @if($platformDefaults['site_og_image'] ?? null)
                                    <img src="{{ asset('storage/'.$platformDefaults['site_og_image']) }}" alt="Platform Default OG Image" class="mx-auto max-h-20 object-contain rounded opacity-75">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-500">{{ __('Currently using the platform default image shown above') }}</p>
                                @endif
                                <div class="flex flex-col items-center justify-center">
                                    <flux:icon.cloud-arrow-up class="size-10 text-neutral-400 mb-2" />
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">
                                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Drop image here') }}</span>
                                        {{ __('or click to browse') }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-500">
                                        {{ __('PNG, JPG, GIF up to 2MB. Recommended: 1200x630 pixels for social sharing') }}
                                    </p>
                                </div>
                                <flux:button as="label" variant="primary" size="sm">
                                    <span class="inline-flex items-center gap-1.5">
                                        <flux:icon.arrow-up-tray class="h-3 w-3 shrink-0" />
                                        <span>{{ __('Select Image') }}</span>
                                    </span>
                                    <input type="file" wire:model="og_image" accept="image/*" class="hidden">
                                </flux:button>
                            </div>
                        @endif
                    </div>
                    <flux:description class="mt-2">
                        {{ __('Upload an image that will be displayed when your website is shared on social media platforms (Facebook, Twitter, LinkedIn, etc.). Recommended size: 1200x630 pixels.') }}
                    </flux:description>
                    <flux:error name="og_image" />
                    <div wire:loading wire:target="og_image" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        {{ __('Uploading...') }}
                    </div>
                </flux:field>
            </div>
        </div>

        <!-- Save Button -->
        <div class="sticky bottom-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between gap-4">
            @if (session()->has('message'))
                <flux:callout variant="success" class="flex-1 mb-0">{{ session('message') }}</flux:callout>
            @else
                <div class="flex-1"></div>
            @endif
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-settings.layout>
