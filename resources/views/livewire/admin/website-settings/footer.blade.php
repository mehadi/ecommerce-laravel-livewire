<x-website-settings.layout :heading="__('Footer')" :subheading="__('Choose how the footer looks across your entire storefront')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Footer Style') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Applies to every page of your storefront. Your logo, tagline, contact details and social links are filled in automatically.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_footer_variant',
                'selected' => $storefront_footer_variant,
                'previewView' => 'livewire.admin.website-settings.footer-preview',
                'defaultKey' => \App\Support\FooterVariants::DEFAULT,
                'accent' => 'emerald',
            ])
            <flux:error name="storefront_footer_variant" />
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
