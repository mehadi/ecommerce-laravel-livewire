{{-- `message` must be specific and descriptive, never a generic "Are you sure?" --}}
@props(['message'])

<flux:button {{ $attributes }} variant="danger" wire:confirm="{{ $message }}">
    {{ $slot }}
</flux:button>
