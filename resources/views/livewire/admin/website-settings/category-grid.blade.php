<x-website-settings.layout :heading="__('Category Grid')" :subheading="__('Choose how categories are displayed on your storefront Categories page')">
    <form wire:submit="update" class="space-y-8">
        <!-- Categories Page Grid -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <flux:icon.rectangle-group class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Categories Page Layout') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Applies to the "Shop by Category" page on your storefront.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_category_grid_variant',
                'selected' => $storefront_category_grid_variant,
                'previewView' => 'livewire.admin.website-settings.category-grid-preview',
                'defaultKey' => \App\Support\CategoryGridVariants::DEFAULT,
            ])
            <flux:error name="storefront_category_grid_variant" />
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
