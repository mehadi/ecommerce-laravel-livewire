<x-website-settings.layout :heading="__('Header')" :subheading="__('Choose how the storefront header looks across your entire site')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <svg class="size-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Header Style') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Applies to every page of your storefront. Your logo, navigation, categories, and cart are filled in automatically.') }}</flux:text>
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

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
