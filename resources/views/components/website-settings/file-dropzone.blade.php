{{-- Drag-and-drop file upload, parameterized from the logo/favicon/og-image markup
     duplicated across website-settings pages. `newFile` is the Livewire temporary
     upload property itself (for ->temporaryUrl() previews), `currentUrl` is the
     already-resolved URL of a previously stored file. `aspect` controls sizing:
     'lg' (site logo), 'wide' (og-image), or 'square' (favicon, the default). --}}
@props([
    'wireModel',
    'label',
    'currentUrl' => null,
    'newFile' => null,
    'removeAction' => null,
    'removeConfirmMessage' => null,
    'aspect' => 'square',
    'accept' => 'image/*',
    'hint' => null,
])

@php
    [$padding, $iconSize, $iconMargin, $previewMaxH, $spacing] = match ($aspect) {
        'lg' => ['p-8', 'size-12', 'mb-4', 'max-h-32', 'space-y-4'],
        'wide' => ['p-6', 'size-10', 'mb-2', 'max-h-32', 'space-y-3'],
        default => ['p-6', 'size-10', 'mb-2', 'max-h-16', 'space-y-3'],
    };
@endphp

<flux:field>
    <flux:label>{{ $label }}</flux:label>

    <div
        x-data="{
            isDragging: false,
            handleDrop(e) {
                e.preventDefault();
                this.isDragging = false;
                if (e.dataTransfer.files.length) {
                    @this.upload('{{ $wireModel }}', e.dataTransfer.files[0]);
                }
            },
            handleDragOver(e) {
                e.preventDefault();
                this.isDragging = true;
            },
            handleDragLeave() {
                this.isDragging = false;
            }
        }"
        @drop.prevent="handleDrop"
        @dragover.prevent="handleDragOver"
        @dragleave.prevent="handleDragLeave"
        :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-zinc-300 dark:border-zinc-600'"
        class="relative border-2 border-dashed rounded-lg {{ $padding }} text-center transition-colors hover:border-zinc-400 dark:hover:border-zinc-500"
    >
        @if($currentUrl || $newFile)
            <div class="{{ $spacing }}">
                @if($newFile)
                    <img src="{{ $newFile->temporaryUrl() }}" alt="{{ $label }}" class="mx-auto {{ $previewMaxH }} object-contain rounded">
                @elseif($currentUrl)
                    <img src="{{ $currentUrl }}" alt="{{ $label }}" class="mx-auto {{ $previewMaxH }} object-contain rounded">
                @endif
                <div class="flex items-center justify-center gap-3">
                    <flux:button as="label" variant="primary" size="sm">
                        <span class="inline-flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 8.25 12 3.75m0 0L7.5 8.25M12 3.75v12" />
                            </svg>
                            <span>{{ __('Change') }}</span>
                        </span>
                        <input type="file" wire:model="{{ $wireModel }}" accept="{{ $accept }}" class="hidden">
                    </flux:button>
                    @if($removeAction && $currentUrl)
                        <flux:button
                            type="button"
                            wire:click="{{ $removeAction }}"
                            wire:confirm="{{ $removeConfirmMessage ?? __('Are you sure you want to remove this file? This cannot be undone.') }}"
                            variant="danger"
                            size="sm"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>{{ __('Remove') }}</span>
                            </span>
                        </flux:button>
                    @endif
                </div>
            </div>
        @else
            <div class="{{ $spacing }}">
                <div class="flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="{{ $iconSize }} text-zinc-400 {{ $iconMargin }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Drop file here') }}</span>
                        {{ __('or click to browse') }}
                    </p>
                    @if($hint)
                        <p class="text-xs text-zinc-500 dark:text-zinc-500">{{ $hint }}</p>
                    @endif
                </div>
                <flux:button as="label" variant="primary" size="sm">
                    <span class="inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 8.25 12 3.75m0 0L7.5 8.25M12 3.75v12" />
                        </svg>
                        <span>{{ __('Select File') }}</span>
                    </span>
                    <input type="file" wire:model="{{ $wireModel }}" accept="{{ $accept }}" class="hidden">
                </flux:button>
            </div>
        @endif
    </div>

    @isset($description)
        <flux:description class="mt-2">{{ $description }}</flux:description>
    @endisset
    <flux:error name="{{ $wireModel }}" />
    <div wire:loading wire:target="{{ $wireModel }}" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
        {{ __('Uploading...') }}
    </div>
</flux:field>
