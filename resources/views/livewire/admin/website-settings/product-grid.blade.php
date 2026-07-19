<x-website-settings.layout :heading="__('Product Grid')" :subheading="__('Choose how products are displayed in your shop and on your homepage')">
    <form wire:submit="update" class="space-y-8">
        <!-- Shop & Category Grid -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <svg class="size-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Shop & Category Grid') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Applies to your Shop page and every category page.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_shop_grid_variant',
                'selected' => $storefront_shop_grid_variant,
                'previewView' => 'livewire.admin.website-settings.product-grid-preview',
                'defaultKey' => \App\Support\ProductGridVariants::DEFAULT,
                'accent' => 'violet',
            ])
            <flux:error name="storefront_shop_grid_variant" />
        </div>

        <!-- Featured Products Grid -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <svg class="size-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0l-4.725 2.885a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Featured Products Grid') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Applies only to the "Featured Products" section on your homepage — independent of the style above.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_featured_grid_variant',
                'selected' => $storefront_featured_grid_variant,
                'previewView' => 'livewire.admin.website-settings.product-grid-preview',
                'defaultKey' => \App\Support\ProductGridVariants::DEFAULT,
                'accent' => 'amber',
            ])
            <flux:error name="storefront_featured_grid_variant" />
        </div>

        <x-website-settings.save-bar action="update" label="{{ __('Save') }}" savingLabel="{{ __('Saving...') }}" />
    </form>
</x-website-settings.layout>
