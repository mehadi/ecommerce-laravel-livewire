@props(['category', 'depth' => 0, 'allCategories' => []])

@php
    $collectDescendantIds = function ($cat) use (&$collectDescendantIds) {
        $ids = [];
        foreach ($cat->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $collectDescendantIds($child));
        }

        return $ids;
    };
    $excludeIds = array_merge([$category->id], $collectDescendantIds($category));
    $moveOptions = collect($allCategories)->reject(fn ($item) => in_array($item['category']->id, $excludeIds, true));
@endphp

<div class="space-y-0.5">
    <div
        class="flex flex-wrap items-center justify-between gap-1.5 {{ $depth === 0 ? 'px-2.5 py-1.5' : 'px-2 py-1' }} bg-zinc-50 dark:bg-zinc-800 rounded-lg transition-all cursor-move"
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
        <div class="flex items-center gap-2 flex-1 min-w-0">
            <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
            <span class="text-sm {{ $depth === 0 ? 'font-semibold text-zinc-900 dark:text-white' : 'text-zinc-700 dark:text-zinc-300' }}">
                @if($depth > 0)
                    <span class="text-zinc-400 dark:text-zinc-500">└─</span>
                @endif
                {{ $category->name_en }}
            </span>
            <flux:badge :variant="$category->is_active ? 'success' : 'danger'" size="sm">
                {{ $category->is_active ? __('Active') : __('Inactive') }}
            </flux:badge>
            @if($category->children->count() > 0)
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    ({{ $category->children->count() }} {{ __('subcategories') }})
                </span>
            @endif
            <span class="text-xs text-blue-600 dark:text-blue-400"
                x-show="isDraggedOver({{ $category->id }})"
                x-transition>
                {{ __('Drop here') }}
            </span>
        </div>
        <div class="flex flex-wrap items-center gap-1.5">
            <div @click.stop @dragstart.stop.prevent>
                <label class="sr-only" for="move-parent-{{ $category->id }}">
                    {{ __('Move :name to a different parent category', ['name' => $category->name_en]) }}
                </label>
                <select id="move-parent-{{ $category->id }}"
                    draggable="false"
                    class="no-drag rounded-md border-zinc-300 bg-white py-0.5 pl-1.5 pr-6 text-xs text-zinc-700 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-300"
                    onchange="if (this.value !== '__placeholder__') { window.Livewire.find('{{ $__livewire->getId() }}').call('updateCategoryParent', {{ $category->id }}, this.value === '' ? null : parseInt(this.value, 10)); }"
                >
                    <option value="__placeholder__" selected>{{ __('Move to...') }}</option>
                    <option value="">{{ __('Main Category (No Parent)') }}</option>
                    @foreach($moveOptions as $item)
                        <option value="{{ $item['category']->id }}">{{ str_repeat('— ', $item['depth']) }}{{ $item['category']->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <flux:button wire:click="openModal({{ $category->id }})" size="xs" variant="ghost"
                @click.stop
                class="no-drag">
                {{ __('Edit') }}
            </flux:button>
        </div>
    </div>

    @if($category->children->count() > 0)
        <div class="ml-4 space-y-0.5 border-l-2 border-zinc-200 dark:border-zinc-700 pl-3">
            @foreach($category->children as $child)
                @include('livewire.admin.categories.partials.tree-item', ['category' => $child, 'depth' => $depth + 1, 'allCategories' => $allCategories])
            @endforeach
        </div>
    @endif
</div>
