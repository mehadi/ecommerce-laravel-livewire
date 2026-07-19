<x-website-settings.layout :heading="__('SEO Settings')" :subheading="__('Optimize your website for search engines')">
    <form wire:submit="update" class="space-y-6">
        <!-- SEO Settings Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-amber-600 dark:text-amber-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('SEO Settings') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Optimize your website for search engines') }}</flux:text>
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
                                {{ $this->metaDescriptionLength }} / 160
                            </span>
                            @if($this->metaDescriptionLength > 0 && $this->metaDescriptionLength < 120)
                                <span class="inline-flex items-center text-amber-600 dark:text-amber-400" role="img" aria-label="{{ __('Too short - aim for 120-160 characters') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                </span>
                            @elseif($this->metaDescriptionLength > 160)
                                <span class="inline-flex items-center text-red-600 dark:text-red-400" role="img" aria-label="{{ __('Too long - may be truncated in search results') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                </span>
                            @elseif($this->metaDescriptionLength >= 120 && $this->metaDescriptionLength <= 160)
                                <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400" role="img" aria-label="{{ __('Optimal length') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
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
                <x-website-settings.file-dropzone
                    wire-model="og_image"
                    :label="__('Open Graph Image')"
                    :current-url="$existing_og_image ? asset('storage/'.$existing_og_image) : null"
                    :new-file="$og_image"
                    remove-action="removeOgImage"
                    :remove-confirm-message="__('Are you sure you want to remove the Open Graph image? This cannot be undone.')"
                    aspect="wide"
                    hint="{{ __('PNG, JPG, GIF up to 2MB. Recommended: 1200x630 pixels for social sharing') }}"
                >
                    <x-slot:description>
                        {{ __('Upload an image that will be displayed when your website is shared on social media platforms (Facebook, Twitter, LinkedIn, etc.). Recommended size: 1200x630 pixels.') }}
                        @if(!$existing_og_image && !$og_image && ($platformDefaults['site_og_image'] ?? null))
                            {{ __('Currently using the platform default image shown in shares until you upload your own.') }}
                        @endif
                    </x-slot:description>
                </x-website-settings.file-dropzone>
            </div>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
