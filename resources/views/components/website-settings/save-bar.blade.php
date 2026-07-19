{{-- Sticky save-bar footer for a website-settings form. Default slot is optional
     leading content (e.g. a note or callout); leave empty for a plain spacer. --}}
@props([
    'action' => 'update',
    'label' => null,
    'savingLabel' => null,
])

<div class="sticky bottom-0 -mx-6 -mb-6 md:mb-0 px-6 py-4 mt-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-zinc-900/70 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4 rounded-b-xl">
    @if($slot->isEmpty())
        <div class="flex-1"></div>
    @else
        <div class="flex-1">{{ $slot }}</div>
    @endif

    <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="{{ $action }}" class="whitespace-nowrap">
        <span wire:loading.remove wire:target="{{ $action }}">{{ $label ?? __('Save') }}</span>
        <span wire:loading wire:target="{{ $action }}">{{ $savingLabel ?? __('Saving...') }}</span>
    </flux:button>
</div>
