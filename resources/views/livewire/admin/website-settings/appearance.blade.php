<x-website-settings.layout :heading="__('Appearance')" :subheading="__('Control how content is displayed to visitors')">
    <form wire:submit="update" class="space-y-8">
        <!-- Appearance Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/20">
                    <svg class="size-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Appearance') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Control how content is displayed to visitors') }}</flux:text>
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
                        class="w-full h-2 rounded-full bg-zinc-200 dark:bg-zinc-700 accent-teal-600 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                    >
                    <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400 mt-1">
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
                        class="w-full h-2 rounded-full bg-zinc-200 dark:bg-zinc-700 accent-teal-600 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                    >
                    <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400 mt-1">
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
                        <input type="color" wire:model.live="theme_primary_color" class="h-10 w-14 rounded-lg border border-zinc-200 dark:border-zinc-700 cursor-pointer" />
                        <flux:input wire:model.live.debounce.400ms="theme_primary_color" type="text" class="flex-1" />
                    </div>
                    <flux:description>{{ __('Used for primary buttons (Add to Cart, Buy Now) and nav accents across the storefront.') }}</flux:description>
                    <flux:error name="theme_primary_color" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Secondary Brand Color') }}</flux:label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model.live="theme_secondary_color" class="h-10 w-14 rounded-lg border border-zinc-200 dark:border-zinc-700 cursor-pointer" />
                        <flux:input wire:model.live.debounce.400ms="theme_secondary_color" type="text" class="flex-1" />
                    </div>
                    <flux:description>{{ __('Used for secondary accents across the storefront.') }}</flux:description>
                    <flux:error name="theme_secondary_color" />
                </flux:field>
            </div>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
