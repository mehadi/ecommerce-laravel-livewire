{{-- Shared "grid of variant preview cards with a radio input" picker, used on
     the Product Grid and Category Grid settings pages. Expects $variants
     (a Variants::all() array), $wireModel (the property name to bind) and
     $selected (the currently chosen key). Optional: $previewView (the
     miniature-schematic partial), $defaultKey (the variant flagged as
     "Default"), which fall back to the product-grid ones, and $accent (the
     selected-state border/badge color, matching the parent section's own
     icon accent — falls back to violet). --}}
@php
    $previewView = $previewView ?? 'livewire.admin.website-settings.product-grid-preview';
    $defaultKey = $defaultKey ?? \App\Support\ProductGridVariants::DEFAULT;
    $accent = $accent ?? 'violet';
    $accentClasses = match ($accent) {
        'emerald' => ['border' => 'border-emerald-600 dark:border-emerald-500', 'badge' => 'bg-emerald-600'],
        'amber' => ['border' => 'border-amber-600 dark:border-amber-500', 'badge' => 'bg-amber-600'],
        'blue' => ['border' => 'border-blue-600 dark:border-blue-500', 'badge' => 'bg-blue-600'],
        'purple' => ['border' => 'border-purple-600 dark:border-purple-500', 'badge' => 'bg-purple-600'],
        'red' => ['border' => 'border-red-600 dark:border-red-500', 'badge' => 'bg-red-600'],
        'indigo' => ['border' => 'border-indigo-600 dark:border-indigo-500', 'badge' => 'bg-indigo-600'],
        'rose' => ['border' => 'border-rose-600 dark:border-rose-500', 'badge' => 'bg-rose-600'],
        default => ['border' => 'border-violet-600 dark:border-violet-500', 'badge' => 'bg-violet-600'],
    };
@endphp
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($variants as $key => $variant)
        <label class="relative flex flex-col cursor-pointer rounded-xl overflow-hidden border-2 transition-all duration-150 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-accent has-[:focus-visible]:ring-offset-2 {{ $selected === $key ? $accentClasses['border'].' shadow-md' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600' }}">
            <input type="radio" wire:model.live="{{ $wireModel }}" value="{{ $key }}" class="sr-only" name="{{ $wireModel }}">

            <div class="aspect-[16/10] bg-zinc-100 dark:bg-zinc-800 p-3">
                @include($previewView, ['variant' => $key])
            </div>

            <div class="flex-1 p-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <p class="text-sm font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    {{ $variant['name'] }}
                    @if($key === $defaultKey)
                        <span class="text-[10px] font-bold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('Default') }}</span>
                    @endif
                </p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">{{ $variant['description'] }}</p>
            </div>

            @if($selected === $key)
                <span class="absolute top-2.5 right-2.5 flex items-center justify-center w-6 h-6 rounded-full {{ $accentClasses['badge'] }} text-white shadow-md" aria-hidden="true">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"></path>
                    </svg>
                </span>
            @endif
        </label>
    @endforeach
</div>
