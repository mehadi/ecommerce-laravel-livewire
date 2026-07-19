<x-website-settings.layout :heading="__('Hero Section')" :subheading="__('Choose how the hero banner at the top of your storefront looks')">
    <form wire:submit="update" class="space-y-8">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <flux:icon.sparkles class="size-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Hero Style') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('The selected style applies to your home and landing pages. Your hero title, text, image, and brand colors are used automatically.') }}</flux:text>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($variants as $key => $variant)
                    <label class="relative flex flex-col cursor-pointer rounded-xl overflow-hidden border-2 transition-all duration-150 {{ $storefront_hero_variant === $key ? 'border-violet-600 dark:border-violet-500 shadow-md' : 'border-neutral-200 dark:border-neutral-700 hover:border-neutral-300 dark:hover:border-neutral-600' }}">
                        <input type="radio" wire:model.live="storefront_hero_variant" value="{{ $key }}" class="sr-only" name="storefront_hero_variant">

                        {{-- Miniature layout preview --}}
                        <div class="aspect-[16/10] bg-zinc-100 dark:bg-zinc-800 p-3">
                            @include('livewire.admin.website-settings.hero-preview', ['variant' => $key])
                        </div>

                        <div class="flex-1 p-4 pt-3 border-t border-neutral-100 dark:border-neutral-800 bg-white dark:bg-zinc-900">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                                {{ $variant['name'] }}
                                @if($key === \App\Support\HeroVariants::DEFAULT)
                                    <span class="text-[10px] font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">{{ __('Default') }}</span>
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">{{ $variant['description'] }}</p>
                        </div>

                        @if($storefront_hero_variant === $key)
                            <span class="absolute top-2.5 right-2.5 flex items-center justify-center w-6 h-6 rounded-full bg-violet-600 text-white shadow-md" aria-hidden="true">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"></path></svg>
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>
            <flux:error name="storefront_hero_variant" />
        </div>

        <!-- Hero Content Card -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/20">
                    <flux:icon.pencil-square class="size-5 text-sky-600 dark:text-sky-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Hero Content') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Customize the badge, buttons, and stats shown in the hero. Leave a field empty to use the default.') }}</flux:text>
                </div>
            </div>

            <flux:callout icon="information-circle" class="text-sm">
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
