@props([
    'wireModel',
    'value' => null,
    'multiple' => false,
    'removeMethod' => null,
    'label' => null,
    'placeholderTitle' => __('Drag & drop files'),
    'placeholderDescription' => null,
    'buttonText' => __('Browse files'),
    'helperText' => null,
    'iconClasses' => 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
    'highlightClass' => 'border-blue-500/80 bg-blue-50/80 dark:border-blue-400/50 dark:bg-blue-900/20',
    'hoverClass' => 'hover:border-blue-500 hover:bg-blue-50/80 dark:hover:border-blue-400/60 dark:hover:bg-blue-900/30',
    'baseClass' => 'border-zinc-300 dark:border-zinc-700/80',
    'badgeText' => null,
    'previewHelper' => null,
    'footnote' => null,
    'secondaryFootnote' => null,
    'emptyHint' => null,
    'loadingText' => __('Uploading...'),
    'loadingClass' => 'text-blue-600 dark:text-blue-400',
    'accept' => 'image/*',
])

@php
    $isMultiple = (bool) $multiple;
    $accessibleLabel = $label ?? $placeholderTitle;

    $uploads = $isMultiple
        ? collect(is_iterable($value) ? $value : [])->filter()->values()
        : collect($value ? [$value] : []);
@endphp

<div class="space-y-4">
    <div
        x-data="{
            isDragging: false,
            browse() { this.$refs.fileInput.click(); },
            handleDrop(event) {
                this.isDragging = false;
                const files = event.dataTransfer?.files ? Array.from(event.dataTransfer.files) : [];
                this.handleFiles(files);
            },
            handleFiles(files) {
                if (!files || !files.length) {
                    return;
                }

                @if($isMultiple)
                    @this.uploadMultiple('{{ $wireModel }}', files);
                @else
                    @this.upload('{{ $wireModel }}', files[0]);
                @endif
            },
            handleDragOver(event) {
                event.preventDefault();
                this.isDragging = true;
            },
            handleDragLeave() {
                this.isDragging = false;
            }
        }"
        @drop.prevent="handleDrop($event)"
        @dragover.prevent="handleDragOver($event)"
        @dragleave.prevent="handleDragLeave"
        @click="browse"
        @keydown.enter.prevent="browse"
        @keydown.space.prevent="browse"
        role="button"
        tabindex="0"
        aria-label="{{ $accessibleLabel }}"
        class="flex cursor-pointer flex-col items-center justify-center gap-4 rounded-2xl border-2 border-dashed bg-zinc-50/70 p-8 text-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:bg-zinc-900/60 dark:focus-visible:ring-offset-zinc-900 {{ $baseClass }} {{ $hoverClass }}"
        :class="isDragging ? '{{ $highlightClass }}' : ''"
    >
        <div class="flex flex-col items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-full {{ $iconClasses }}" aria-hidden="true">
                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9h.008v.008H8.25V9zM21 15l-5.197-5.197a1 1 0 00-1.414 0L9 15" />
                </svg>
            </div>

            <div class="space-y-1">
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-100">{{ $placeholderTitle }}</p>

                @if($placeholderDescription)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $placeholderDescription }}</p>
                @endif
            </div>

            <flux:button type="button" size="xs" variant="outline" @click.stop="browse" tabindex="-1">
                {{ $buttonText }}
            </flux:button>

            @if($helperText)
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $helperText }}</p>
            @endif
        </div>

        <input
            x-ref="fileInput"
            type="file"
            class="sr-only"
            tabindex="-1"
            aria-hidden="true"
            wire:model="{{ $wireModel }}"
            {{ $isMultiple ? 'multiple' : '' }}
            accept="{{ $accept }}"
            @change="handleFiles($event.target.files ? Array.from($event.target.files) : [])"
        >
    </div>

    @if(! $isMultiple && $uploads->isNotEmpty())
        @php
            $file = $uploads->first();
        @endphp

        <div class="space-y-2">
            <div class="relative w-full max-w-sm overflow-hidden rounded-xl border border-zinc-200 shadow-sm dark:border-zinc-700">
                <img src="{{ $file->temporaryUrl() }}" class="h-60 w-full object-cover" alt="{{ __('Upload preview') }}">

                @if($badgeText)
                    <span class="absolute left-3 top-3 rounded-full bg-white/85 px-2 py-0.5 text-xs font-semibold uppercase text-zinc-700 dark:bg-black/60 dark:text-zinc-100">
                        {{ $badgeText }}
                    </span>
                @endif

                @if($removeMethod)
                    <button
                        type="button"
                        wire:click="{{ $removeMethod }}"
                        class="absolute right-3 top-3 inline-flex min-h-10 items-center gap-1 rounded-full bg-zinc-900/75 px-3 py-1 text-xs font-medium text-white backdrop-blur transition hover:bg-red-600/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V5a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Remove') }}
                    </button>
                @endif
            </div>

            @if($previewHelper)
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $previewHelper }}</p>
            @endif
        </div>
    @elseif($isMultiple && $uploads->isNotEmpty())
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($uploads as $index => $file)
                <div class="group relative overflow-hidden rounded-xl border border-zinc-200 shadow-sm transition hover:shadow-md dark:border-zinc-700" wire:key="{{ $wireModel }}-preview-{{ $index }}">
                    <img src="{{ $file->temporaryUrl() }}" class="h-40 w-full object-cover" alt="{{ __('Upload preview :number', ['number' => $loop->iteration]) }}">

                    @if($removeMethod)
                        <button
                            type="button"
                            wire:click="{{ $removeMethod }}({{ $index }})"
                            class="absolute right-2 top-2 inline-flex min-h-10 min-w-10 items-center justify-center rounded-full bg-zinc-900/70 p-2 text-white backdrop-blur transition hover:bg-red-600/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="sr-only">{{ __('Remove image') }}</span>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif($emptyHint)
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $emptyHint }}</p>
    @endif

    @if($footnote || $secondaryFootnote)
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-500 dark:text-zinc-400">
            @if($footnote)
                <span>{{ $footnote }}</span>
            @endif

            @if($secondaryFootnote)
                <span>{{ $secondaryFootnote }}</span>
            @endif
        </div>
    @endif

    <div wire:loading wire:target="{{ $wireModel }}" class="text-xs font-medium {{ $loadingClass }}" role="status">
        {{ $loadingText }}
    </div>
</div>
