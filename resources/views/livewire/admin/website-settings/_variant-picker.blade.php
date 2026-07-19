{{-- Shared "grid of variant preview cards with a radio input" picker, used on
     the Product Grid and Category Grid settings pages. Expects $variants
     (a Variants::all() array), $wireModel (the property name to bind) and
     $selected (the currently chosen key). Optional: $previewView (the
     miniature-schematic partial) and $defaultKey (the variant flagged as
     "Default"), which fall back to the product-grid ones. --}}
@php
    $previewView = $previewView ?? 'livewire.admin.website-settings.product-grid-preview';
    $defaultKey = $defaultKey ?? \App\Support\ProductGridVariants::DEFAULT;
@endphp
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($variants as $key => $variant)
        <label class="relative flex flex-col cursor-pointer rounded-xl overflow-hidden border-2 transition-all duration-150 {{ $selected === $key ? 'border-violet-600 dark:border-violet-500 shadow-md' : 'border-neutral-200 dark:border-neutral-700 hover:border-neutral-300 dark:hover:border-neutral-600' }}">
            <input type="radio" wire:model.live="{{ $wireModel }}" value="{{ $key }}" class="sr-only" name="{{ $wireModel }}">

            <div class="aspect-[16/10] bg-zinc-100 dark:bg-zinc-800 p-3">
                @include($previewView, ['variant' => $key])
            </div>

            <div class="flex-1 p-4 pt-3 border-t border-neutral-100 dark:border-neutral-800 bg-white dark:bg-zinc-900">
                <p class="text-sm font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    {{ $variant['name'] }}
                    @if($key === $defaultKey)
                        <span class="text-[10px] font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">{{ __('Default') }}</span>
                    @endif
                </p>
                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">{{ $variant['description'] }}</p>
            </div>

            @if($selected === $key)
                <span class="absolute top-2.5 right-2.5 flex items-center justify-center w-6 h-6 rounded-full bg-violet-600 text-white shadow-md" aria-hidden="true">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"></path></svg>
                </span>
            @endif
        </label>
    @endforeach
</div>
