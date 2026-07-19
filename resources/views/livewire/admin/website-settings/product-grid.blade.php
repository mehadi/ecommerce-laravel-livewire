<x-website-settings.layout :heading="__('Product Grid')" :subheading="__('Choose how products are displayed in your shop and on your homepage')">
    <form wire:submit="update" class="space-y-8">
        <!-- Shop & Category Grid -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <flux:icon.squares-2x2 class="size-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Shop & Category Grid') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Applies to your Shop page and every category page.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_shop_grid_variant',
                'selected' => $storefront_shop_grid_variant,
            ])
            <flux:error name="storefront_shop_grid_variant" />
        </div>

        <!-- Featured Products Grid -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <flux:icon.star class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Featured Products Grid') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Applies only to the "Featured Products" section on your homepage — independent of the style above.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_featured_grid_variant',
                'selected' => $storefront_featured_grid_variant,
            ])
            <flux:error name="storefront_featured_grid_variant" />
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
</x-website-settings.layout>
