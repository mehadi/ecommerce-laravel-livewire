<x-website-settings.layout :heading="__('Product Details Page')" :subheading="__('Choose how the single-product page (image, price, and buy box) is laid out')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 4.5h16.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Zm10.5 4.5h.008v.008h-.008V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Product Details Layout') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Applies to every product page and the buy-now landing page funnel.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_product_details_variant',
                'selected' => $storefront_product_details_variant,
                'previewView' => 'livewire.admin.website-settings.product-details-preview',
                'defaultKey' => \App\Support\ProductDetailsVariants::DEFAULT,
                'accent' => 'emerald',
            ])
            <flux:error name="storefront_product_details_variant" />
        </div>

        <x-website-settings.save-bar action="update" label="{{ __('Save') }}" savingLabel="{{ __('Saving...') }}" />
    </form>
</x-website-settings.layout>
