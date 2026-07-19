@props(['heading', 'description' => null])

<div class="flex flex-wrap justify-between items-center gap-4">
    <div>
        <flux:heading>{{ $heading }}</flux:heading>
        @if($description)
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ $description }}
            </flux:text>
        @endif
    </div>
    {{ $slot }}
</div>
