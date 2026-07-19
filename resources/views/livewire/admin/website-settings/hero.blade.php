<x-website-settings.layout :heading="__('Hero Section')" :subheading="__('Choose how the hero banner at the top of your storefront looks')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <svg class="size-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Hero Style') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('The selected style applies to your home and landing pages. Your hero title, text, image, and brand colors are used automatically.') }}</flux:text>
                </div>
            </div>

            @include('livewire.admin.website-settings._variant-picker', [
                'variants' => $variants,
                'wireModel' => 'storefront_hero_variant',
                'selected' => $storefront_hero_variant,
                'previewView' => 'livewire.admin.website-settings.hero-preview',
                'defaultKey' => \App\Support\HeroVariants::DEFAULT,
            ])
            <flux:error name="storefront_hero_variant" />
        </div>

        <!-- Hero Content Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/20">
                    <svg class="size-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Hero Content') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Customize the badge, buttons, and stats shown in the hero. Leave a field empty to use the default.') }}</flux:text>
                </div>
            </div>

            <flux:callout variant="info" icon="information-circle" class="text-sm">
                {{ __('The hero title, description, and image are managed on the') }}
                <flux:link :href="route('admin.sections.index')" wire:navigate>{{ __('Sections') }}</flux:link>
                {{ __('page (section type "Hero").') }}
            </flux:callout>

            <flux:field>
                <flux:label>{{ __('Badge Text') }}</flux:label>
                <flux:input wire:model="hero_badge_text" type="text" placeholder="{{ __('100% Natural & Premium Quality') }}" />
                <flux:description>{{ __('The small pill shown above the hero title.') }}</flux:description>
                <flux:error name="hero_badge_text" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <flux:field>
                    <flux:label>{{ __('Primary Button Label') }}</flux:label>
                    <flux:input wire:model="hero_primary_cta_label" type="text" placeholder="{{ __('Order Now') }}" />
                    <flux:error name="hero_primary_cta_label" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Primary Button Link') }}</flux:label>
                    <flux:input wire:model="hero_primary_cta_url" type="text" placeholder="#product" />
                    <flux:description>{{ __('An anchor (#product), a page path (/shop), or a full URL.') }}</flux:description>
                    <flux:error name="hero_primary_cta_url" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Secondary Button Label') }}</flux:label>
                    <flux:input wire:model="hero_secondary_cta_label" type="text" placeholder="{{ __('Browse Shop') }}" />
                    <flux:description>{{ __('Shown on every style except Bento Grid.') }}</flux:description>
                    <flux:error name="hero_secondary_cta_label" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Secondary Button Link') }}</flux:label>
                    <flux:input wire:model="hero_secondary_cta_url" type="text" placeholder="/shop" />
                    <flux:error name="hero_secondary_cta_url" />
                </flux:field>
            </div>

            <flux:switch wire:model="hero_show_stats" :label="__('Show store stats')" :description="__('Orders delivered, product count, and review rating shown in the hero. Bento Grid always shows its stat cards.')" />
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
