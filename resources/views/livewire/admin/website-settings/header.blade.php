<x-website-settings.layout :heading="__('Header')" :subheading="__('Choose how the storefront header looks across your entire site')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <flux:icon.bars-3 class="size-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Header Style') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Applies to every page of your storefront. Your logo, navigation, categories, and cart are filled in automatically.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_header_variant',
                'selected' => $storefront_header_variant,
                'previewView' => 'livewire.admin.website-settings.header-preview',
                'defaultKey' => \App\Support\NavbarVariants::DEFAULT,
            ])
            <flux:error name="storefront_header_variant" />

            @if($storefront_header_variant === \App\Support\NavbarVariants::DEFAULT)
                <flux:callout variant="info" icon="information-circle" class="text-sm">
                    {{ __('The Floating Pill style can be fine-tuned further on the') }}
                    <flux:link :href="route('admin.navigation.index')" wire:navigate>{{ __('Navigation Layout') }}</flux:link>
                    {{ __('page — reorder, hide, or resize its individual components.') }}
                </flux:callout>
            @endif
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
