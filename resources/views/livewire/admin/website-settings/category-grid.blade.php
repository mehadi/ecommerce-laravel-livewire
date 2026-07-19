<x-website-settings.layout :heading="__('Category Grid')" :subheading="__('Choose how categories are displayed on your storefront Categories page')">
    <form wire:submit="update" class="space-y-8">
        <!-- Categories Page Grid -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 13.5h6.75v6.75H13.5v-6.75ZM13.5 3.75h6.75V10.5H13.5V3.75Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Categories Page Layout') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Applies to the "Shop by Category" page on your storefront.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_category_grid_variant',
                'selected' => $storefront_category_grid_variant,
                'previewView' => 'livewire.admin.website-settings.category-grid-preview',
                'defaultKey' => \App\Support\CategoryGridVariants::DEFAULT,
                'accent' => 'emerald',
            ])
            <flux:error name="storefront_category_grid_variant" />
        </div>

        <x-website-settings.save-bar action="update" label="{{ __('Save') }}" savingLabel="{{ __('Saving...') }}" />
    </form>
</x-website-settings.layout>
