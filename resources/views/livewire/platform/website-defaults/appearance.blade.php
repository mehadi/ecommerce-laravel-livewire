<x-website-defaults.layout :heading="__('Appearance')" :subheading="__('Control how content is displayed to visitors')">
    <form wire:submit="update" class="space-y-8">
        <!-- Appearance Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/20">
                    <flux:icon.language class="size-5 text-teal-600 dark:text-teal-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Appearance') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Control how content is displayed to visitors') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Frontend Text Size') }}</flux:label>
                <flux:radio.group wire:model.live="frontend_text_size" variant="segmented">
                    <flux:radio value="xs" label="{{ __('Extra Small') }}" />
                    <flux:radio value="sm" label="{{ __('Small') }}" />
                    <flux:radio value="medium" label="{{ __('Medium') }}" />
                    <flux:radio value="lg" label="{{ __('Large') }}" />
                    <flux:radio value="xl" label="{{ __('Extra Large') }}" />
                    <flux:radio value="xxl" label="{{ __('Extra Extra Large') }}" />
                    <flux:radio value="custom" label="{{ __('Dynamic') }}" />
                </flux:radio.group>
                <flux:description>{{ __('Adjusts the base text size across the storefront (product pages, shop, and landing pages) for all visitors.') }}</flux:description>
                <flux:error name="frontend_text_size" />
            </flux:field>

            @if($frontend_text_size === 'custom')
                <flux:field>
                    <flux:label>{{ __('Dynamic Size') }} ({{ $frontend_text_size_custom }}%)</flux:label>
                    <input
                        type="range"
                        wire:model.live="frontend_text_size_custom"
                        min="50"
                        max="200"
                        step="5"
                        class="w-full h-2 rounded-full bg-neutral-200 dark:bg-neutral-700 accent-blue-600 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                    >
                    <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        <span>50%</span>
                        <span>100%</span>
                        <span>200%</span>
                    </div>
                    <flux:description>{{ __('Set an exact base text size as a percentage of the default. 100% is the standard size.') }}</flux:description>
                    <flux:error name="frontend_text_size_custom" />
                </flux:field>
            @endif

            <flux:field>
                <flux:label>{{ __('Frontend Content Width') }}</flux:label>
                <flux:radio.group wire:model.live="frontend_content_width" variant="segmented">
                    <flux:radio value="narrow" label="{{ __('Narrow') }}" />
                    <flux:radio value="medium" label="{{ __('Medium') }}" />
                    <flux:radio value="wide" label="{{ __('Wide') }}" />
                    <flux:radio value="xl" label="{{ __('Extra Wide') }}" />
                    <flux:radio value="xxl" label="{{ __('Extra Extra Wide') }}" />
                    <flux:radio value="full" label="{{ __('Full') }}" />
                    <flux:radio value="custom" label="{{ __('Dynamic') }}" />
                </flux:radio.group>
                <flux:description>{{ __('Controls the maximum width of the main content area across the storefront (navigation, sections, shop, and product pages).') }}</flux:description>
                <flux:error name="frontend_content_width" />
            </flux:field>

            @if($frontend_content_width === 'custom')
                <flux:field>
                    <flux:label>{{ __('Dynamic Width') }} ({{ $frontend_content_width_custom }}px)</flux:label>
                    <input
                        type="range"
                        wire:model.live="frontend_content_width_custom"
                        min="960"
                        max="1920"
                        step="10"
                        class="w-full h-2 rounded-full bg-neutral-200 dark:bg-neutral-700 accent-blue-600 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                    >
                    <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        <span>960px</span>
                        <span>1440px</span>
                        <span>1920px</span>
                    </div>
                    <flux:description>{{ __('Set an exact maximum content width in pixels.') }}</flux:description>
                    <flux:error name="frontend_content_width_custom" />
                </flux:field>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <flux:field>
                    <flux:label>{{ __('Primary Brand Color') }}</flux:label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model.live="theme_primary_color" class="h-10 w-14 rounded-lg border border-neutral-200 dark:border-neutral-700 cursor-pointer" />
                        <flux:input wire:model.live="theme_primary_color" type="text" class="flex-1" />
                    </div>
                    <flux:description>{{ __('Used for primary buttons (Add to Cart, Buy Now) and nav accents across the storefront.') }}</flux:description>
                    <flux:error name="theme_primary_color" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Secondary Brand Color') }}</flux:label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model.live="theme_secondary_color" class="h-10 w-14 rounded-lg border border-neutral-200 dark:border-neutral-700 cursor-pointer" />
                        <flux:input wire:model.live="theme_secondary_color" type="text" class="flex-1" />
                    </div>
                    <flux:description>{{ __('Used for secondary accents across the storefront.') }}</flux:description>
                    <flux:error name="theme_secondary_color" />
                </flux:field>
            </div>
        </div>

        <!-- Save Button -->
        <div class="sticky bottom-0 -mx-6 -mb-6 md:mb-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between gap-4 rounded-b-xl">
            <div class="flex-1"></div>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap">
                <span wire:loading.remove wire:target="update">{{ __('Save') }}</span>
                <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</x-website-defaults.layout>
