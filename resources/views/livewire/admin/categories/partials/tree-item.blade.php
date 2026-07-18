@props(['category', 'depth' => 0])

<div class="space-y-1">
    <div
        class="flex items-center justify-between {{ $depth === 0 ? 'p-3' : 'p-2' }} bg-gray-50 dark:bg-zinc-800 rounded-lg transition-all cursor-move"
        :class="{
            'ring-2 ring-blue-500 dark:ring-blue-400 bg-blue-50 dark:bg-blue-900/20': isDraggedOver({{ $category->id }}),
            'opacity-50 ring-2 ring-blue-500 dark:ring-blue-400': isDragging({{ $category->id }})
        }"
        draggable="true"
        @dragstart="handleDragStart($event, {{ $category->id }}, {{ $category->parent_id ?? 'null' }})"
        @dragend="handleDragEnd($event)"
        @dragover.prevent.stop="handleDragOver($event, {{ $category->id }})"
        @dragleave.stop="handleDragLeave"
        @drop.prevent.stop="handleDrop($event, {{ $category->id }})"
    >
        <div class="flex items-center gap-3 flex-1">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
            <span class="{{ $depth === 0 ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300' }}">
                @if($depth > 0)
                    <span class="text-gray-400 dark:text-gray-500">└─</span>
                @endif
                {{ $category->name_en }}
            </span>
            <flux:badge :variant="$category->is_active ? 'success' : 'danger'" size="sm">
                {{ $category->is_active ? __('Active') : __('Inactive') }}
            </flux:badge>
            @if($category->children->count() > 0)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    ({{ $category->children->count() }} {{ __('subcategories') }})
                </span>
            @endif
            <span class="text-xs text-blue-600 dark:text-blue-400"
                x-show="isDraggedOver({{ $category->id }})"
                x-transition>
                {{ __('Drop here') }}
            </span>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="openModal({{ $category->id }})" size="sm" variant="ghost"
                @click.stop
                class="no-drag">
                {{ __('Edit') }}
            </flux:button>
        </div>
    </div>

    @if($category->children->count() > 0)
        <div class="ml-6 space-y-1 border-l-2 border-gray-200 dark:border-zinc-700 pl-4">
            @foreach($category->children as $child)
                @include('livewire.admin.categories.partials.tree-item', ['category' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
