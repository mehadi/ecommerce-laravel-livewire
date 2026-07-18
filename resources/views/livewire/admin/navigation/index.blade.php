<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading>{{ __('Navigation Settings') }}</flux:heading>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Build the navbar layout visually, and manage the links shown inside it') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Changes are automatically reflected on the homepage navigation bar') }}</p>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="clearCache" variant="ghost" size="sm">
                {{ __('Clear Cache') }}
            </flux:button>
            <flux:button wire:click="openModal(null)" variant="primary">
                {{ __('Add Navigation Item') }}
            </flux:button>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <!-- Navbar Layout Builder -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ __('Navbar Layout') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($activeZone === 'desktop')
                        {{ __('Drag components into Start, Middle, or End, and toggle visibility.') }}
                    @else
                        {{ __('Drag components to reorder, resize their grid span (1-12), and toggle visibility.') }}
                    @endif
                </p>
            </div>
            <div class="flex items-center rounded-lg bg-gray-100 dark:bg-zinc-800 p-1">
                <flux:button wire:click="setActiveZone('desktop')" size="sm" :variant="$activeZone === 'desktop' ? 'primary' : 'ghost'">
                    {{ __('Desktop Bar') }}
                </flux:button>
                <flux:button wire:click="setActiveZone('mobile')" size="sm" :variant="$activeZone === 'mobile' ? 'primary' : 'ghost'">
                    {{ __('Mobile Menu') }}
                </flux:button>
            </div>
        </div>

        @if($activeZone === 'desktop')
            <!-- Live schematic preview: Start (compact) | Middle (flexible) | End (compact) -->
            <div class="mb-6">
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">{{ __('Preview') }}</div>
                <div class="flex items-stretch gap-2 p-3 rounded-lg border-2 border-dashed border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50 min-h-[3.5rem]">
                    <div class="flex items-center gap-1 shrink-0">
                        @foreach($navbarRegions['start'] as $navComponent)
                            @if($navComponent->is_visible_desktop)
                                <span class="px-2 py-2 rounded bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 text-[11px] font-semibold whitespace-nowrap">{{ $navComponent->label }}</span>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex items-center gap-1 flex-1 min-w-0 px-2 border-x border-dashed border-gray-300 dark:border-zinc-700">
                        @foreach($navbarRegions['middle'] as $navComponent)
                            @if($navComponent->is_visible_desktop)
                                <span class="px-2 py-2 rounded bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 text-[11px] font-semibold whitespace-nowrap">{{ $navComponent->label }}</span>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @foreach($navbarRegions['end'] as $navComponent)
                            @if($navComponent->is_visible_desktop)
                                <span class="px-2 py-2 rounded bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 text-[11px] font-semibold whitespace-nowrap">{{ $navComponent->label }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Three drop zones: Start / Middle / End -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4"
                x-data="{
                    draggedId: null,
                    draggedOverId: null,
                    dragPosition: null,
                    dragOverRegion: null,
                    handleDragStart(event, id) {
                        this.draggedId = id;
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', id.toString());
                        event.currentTarget.style.opacity = '0.5';
                    },
                    handleDragEnd(event) {
                        event.currentTarget.style.opacity = '1';
                        this.draggedId = null;
                        this.draggedOverId = null;
                        this.dragOverRegion = null;
                    },
                    handleRegionDragOver(event, region) {
                        event.preventDefault();
                        if (!this.draggedId) return;
                        this.dragOverRegion = region;
                        this.draggedOverId = null;
                    },
                    handleRegionDragLeave(event) {
                        const related = event.relatedTarget;
                        if (!related || !event.currentTarget.contains(related)) {
                            this.dragOverRegion = null;
                        }
                    },
                    handleItemDragOver(event, id) {
                        event.preventDefault();
                        event.stopPropagation();
                        if (!this.draggedId || this.draggedId === id) return;
                        this.draggedOverId = id;
                        this.dragOverRegion = null;
                        const rect = event.currentTarget.getBoundingClientRect();
                        this.dragPosition = (event.clientY - rect.top) / rect.height < 0.5 ? 'before' : 'after';
                    },
                    handleDrop(event, region, targetId) {
                        event.preventDefault();
                        if (!this.draggedId) return;
                        const container = event.currentTarget.closest('[data-region-list]');
                        let ids = Array.from(container.querySelectorAll('[data-component-id]')).map(el => parseInt(el.getAttribute('data-component-id')));
                        ids = ids.filter(id => id !== parseInt(this.draggedId));
                        let to = ids.length;
                        if (targetId !== null) {
                            const targetIndex = ids.indexOf(parseInt(targetId));
                            to = targetIndex === -1 ? ids.length : targetIndex + (this.dragPosition === 'after' ? 1 : 0);
                        }
                        ids.splice(to, 0, parseInt(this.draggedId));
                        @this.call('moveComponentToRegion', parseInt(this.draggedId), region, ids);
                        this.draggedId = null;
                        this.draggedOverId = null;
                        this.dragOverRegion = null;
                    }
                }"
            >
                @foreach(['start' => __('Start'), 'middle' => __('Middle'), 'end' => __('End')] as $regionKey => $regionLabel)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $regionLabel }}</h4>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $regionKey === 'middle' ? __('long') : __('short') }}</span>
                        </div>
                        <div
                            class="space-y-2 min-h-[4.5rem] p-2 rounded-lg border-2 border-dashed transition-colors"
                            :class="dragOverRegion === '{{ $regionKey }}' ? 'border-blue-400 dark:border-blue-500 bg-blue-50/40 dark:bg-blue-900/10' : 'border-gray-200 dark:border-zinc-700'"
                            data-region-list
                            @dragover="handleRegionDragOver($event, '{{ $regionKey }}')"
                            @dragleave="handleRegionDragLeave($event)"
                            @drop="handleDrop($event, '{{ $regionKey }}', null)"
                        >
                            @forelse($navbarRegions[$regionKey] as $navComponent)
                                <div
                                    data-component-id="{{ $navComponent->id }}"
                                    draggable="true"
                                    @dragstart="handleDragStart($event, {{ $navComponent->id }})"
                                    @dragend="handleDragEnd($event)"
                                    @dragover="handleItemDragOver($event, {{ $navComponent->id }})"
                                    @drop.stop="handleDrop($event, '{{ $regionKey }}', {{ $navComponent->id }})"
                                    class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 cursor-move hover:border-blue-300 dark:hover:border-blue-600 transition-colors {{ ! $navComponent->is_visible_desktop ? 'opacity-50' : '' }}"
                                    :class="{
                                        'shadow-[inset_0_3px_0_0_#3b82f6]': draggedOverId == {{ $navComponent->id }} && dragPosition === 'before',
                                        'shadow-[inset_0_-3px_0_0_#3b82f6]': draggedOverId == {{ $navComponent->id }} && dragPosition === 'after'
                                    }"
                                >
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                    <span class="flex-1 min-w-0 text-sm font-medium text-gray-900 dark:text-white truncate">{{ $navComponent->label }}</span>
                                    <flux:button wire:click="toggleComponentVisibility({{ $navComponent->id }}, 'desktop')" size="sm" variant="ghost" title="{{ $navComponent->is_visible_desktop ? __('Hide') : __('Show') }}">
                                        @if($navComponent->is_visible_desktop)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                            </svg>
                                        @endif
                                    </flux:button>
                                </div>
                            @empty
                                <div class="text-xs text-gray-400 dark:text-gray-500 text-center py-4 pointer-events-none">{{ __('Drop here') }}</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Live schematic preview -->
            <div class="mb-6">
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">{{ __('Preview') }}</div>
                <div class="grid grid-cols-12 gap-1 p-3 rounded-lg border-2 border-dashed border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50 min-h-[3.5rem]">
                    @foreach($navbarComponents as $navComponent)
                        @if($navComponent->is_visible_mobile)
                            <div style="grid-column: span {{ $navComponent->span_mobile }} / span {{ $navComponent->span_mobile }};" class="px-2 py-3 rounded bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 text-[11px] font-semibold text-center truncate">
                                {{ $navComponent->label }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Draggable component list -->
            <div class="space-y-2"
                x-data="{
                    draggedId: null,
                    draggedOverId: null,
                    dragPosition: null,
                    handleDragStart(event, id) {
                        this.draggedId = id;
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', id.toString());
                        event.currentTarget.style.opacity = '0.5';
                    },
                    handleDragEnd(event) {
                        event.currentTarget.style.opacity = '1';
                        this.draggedId = null;
                        this.draggedOverId = null;
                    },
                    handleDragOver(event, id) {
                        event.preventDefault();
                        if (!this.draggedId || this.draggedId === id) return;
                        this.draggedOverId = id;
                        const rect = event.currentTarget.getBoundingClientRect();
                        this.dragPosition = (event.clientY - rect.top) / rect.height < 0.5 ? 'before' : 'after';
                    },
                    handleDrop(event, targetId) {
                        event.preventDefault();
                        if (!this.draggedId || this.draggedId === targetId) return;
                        const container = event.currentTarget.closest('[data-component-list]');
                        let ids = Array.from(container.querySelectorAll('[data-component-id]')).map(el => parseInt(el.getAttribute('data-component-id')));
                        ids = ids.filter(id => id !== parseInt(this.draggedId));
                        const targetIndex = ids.indexOf(parseInt(targetId));
                        const to = targetIndex === -1 ? ids.length : targetIndex + (this.dragPosition === 'after' ? 1 : 0);
                        ids.splice(to, 0, parseInt(this.draggedId));
                        @this.call('updateComponentOrder', ids, 'mobile');
                        this.draggedId = null;
                        this.draggedOverId = null;
                    }
                }"
                data-component-list
            >
                @foreach($navbarComponents as $navComponent)
                    <div
                        data-component-id="{{ $navComponent->id }}"
                        draggable="true"
                        @dragstart="handleDragStart($event, {{ $navComponent->id }})"
                        @dragend="handleDragEnd($event)"
                        @dragover="handleDragOver($event, {{ $navComponent->id }})"
                        :class="{
                            'shadow-[inset_0_3px_0_0_#3b82f6]': draggedOverId == {{ $navComponent->id }} && dragPosition === 'before',
                            'shadow-[inset_0_-3px_0_0_#3b82f6]': draggedOverId == {{ $navComponent->id }} && dragPosition === 'after'
                        }"
                        @drop="handleDrop($event, {{ $navComponent->id }})"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 cursor-move hover:border-blue-300 dark:hover:border-blue-600 transition-colors {{ ! $navComponent->is_visible_mobile ? 'opacity-50' : '' }}"
                    >
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                        </svg>

                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $navComponent->label }}</div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <flux:button wire:click="updateComponentSpan({{ $navComponent->id }}, 'mobile', -1)" size="sm" variant="ghost" :disabled="$navComponent->span_mobile <= 1">&minus;</flux:button>
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-400 w-12 text-center">{{ $navComponent->span_mobile }}/12</span>
                            <flux:button wire:click="updateComponentSpan({{ $navComponent->id }}, 'mobile', 1)" size="sm" variant="ghost" :disabled="$navComponent->span_mobile >= 12">+</flux:button>
                        </div>

                        <flux:button wire:click="toggleComponentVisibility({{ $navComponent->id }}, 'mobile')" size="sm" variant="ghost" title="{{ $navComponent->is_visible_mobile ? __('Hide') : __('Show') }}">
                            @if($navComponent->is_visible_mobile)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            @endif
                        </flux:button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Two Column Layout: Categories Left, Navigation Right -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Side: Categories List -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6 sticky top-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ __('Categories') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Drag to navigation menu or click to add') }}</p>
                </div>
                
                <div class="mb-4">
                    <flux:field>
                        <flux:input 
                            wire:model.live.debounce.300ms="categorySearch" 
                            placeholder="{{ __('Search categories...') }}"
                            type="search"
                        />
                    </flux:field>
                </div>

                @if($availableCategories->count() > 0)
                    <div 
                        class="space-y-2 max-h-[calc(100vh-300px)] overflow-y-auto"
                        x-data="{
                            handleCategoryDragStart(event, categoryId) {
                                // Store globally so navigation zone can access it
                                window.draggedCategoryId = categoryId;
                                event.dataTransfer.effectAllowed = 'move';
                                event.dataTransfer.setData('text/plain', 'category-' + categoryId);
                                event.dataTransfer.setData('category-id', categoryId);
                                
                                const card = event.currentTarget.closest('[data-category-id]');
                                if (card) {
                                    card.style.opacity = '0.5';
                                    card.style.transform = 'scale(0.95)';
                                }
                            },
                            handleCategoryDragEnd(event) {
                                const card = event.currentTarget.closest('[data-category-id]');
                                if (card) {
                                    card.style.opacity = '1';
                                    card.style.transform = 'scale(1)';
                                }
                                
                                // Clear global variable
                                window.draggedCategoryId = null;
                            }
                        }"
                    >
                        @foreach($availableCategories as $category)
                            @php
                                $tooltip = $category->parent_id ? __('Child of').': '.($category->parent->name_en ?? '') : '';
                            @endphp
                            <div
                                data-category-id="{{ $category->id }}"
                                draggable="true"
                                @dragstart="handleCategoryDragStart($event, {{ $category->id }})"
                                @dragend="handleCategoryDragEnd($event)"
                                @click="window.dispatchEvent(new CustomEvent('category-click', { detail: { id: {{ $category->id }} } }))"
                                class="group w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gradient-to-r from-gray-50 to-gray-50/50 dark:from-zinc-800 dark:to-zinc-800/50 hover:from-blue-50 hover:to-blue-50/50 dark:hover:from-blue-900/20 dark:hover:to-blue-900/10 hover:border-blue-400 dark:hover:border-blue-600 hover:shadow-md transition-all cursor-move {{ $category->parent_id ? 'ml-6 border-l-4 border-l-blue-400 dark:border-l-blue-500' : '' }}"
                                @if($tooltip) title="{{ $tooltip }}" @endif
                            >
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <!-- Drag Handle -->
                                    <div class="flex-shrink-0 cursor-grab active:cursor-grabbing">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    
                                    @if($category->parent_id)
                                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @else
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $category->name_en }}</div>
                                        @if($category->name_bn)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $category->name_bn }}</div>
                                        @endif
                                        @if($category->parent_id)
                                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                {{ __('Child of') }}: {{ $category->parent->name_en }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div class="hidden group-hover:block px-2 py-1 bg-green-100 dark:bg-green-900/30 rounded text-xs font-medium text-green-700 dark:text-green-400">
                                        {{ __('Add') }}
                                    </div>
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(isset($hasMoreCategories) && $hasMoreCategories)
                        <div class="mt-4 text-center">
                            <flux:button wire:click="$set('showAllCategories', true)" variant="ghost" size="sm">
                                {{ __('Show All') }} ({{ $totalCategoryCount - $availableCategories->count() }} {{ __('more') }})
                            </flux:button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-lg">
                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if($categorySearch)
                                {{ __('No categories found.') }}
                            @else
                                {{ __('All categories added.') }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Navigation Items Drag & Drop Zone -->
        <div class="lg:col-span-2">
            <!-- Search and Bulk Actions -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-4 mb-4">
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div class="flex-1 w-full sm:max-w-md">
                        <flux:field>
                            <flux:input 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="{{ __('Search navigation items...') }}"
                                icon="magnifying-glass"
                            />
                        </flux:field>
                    </div>
                    
                    @if(count($selectedItems) > 0)
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ count($selectedItems) }} {{ __('selected') }}
                            </span>
                            <flux:button wire:click="bulkActivate" size="sm" variant="ghost">
                                {{ __('Activate') }}
                            </flux:button>
                            <flux:button wire:click="bulkDeactivate" size="sm" variant="ghost">
                                {{ __('Deactivate') }}
                            </flux:button>
                            <flux:button 
                                wire:click="bulkDelete" 
                                wire:confirm="{{ __('Are you sure you want to delete the selected items?') }}"
                                size="sm" 
                                variant="danger"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                            <flux:button wire:click="$set('selectedItems', [])" size="sm" variant="ghost">
                                {{ __('Clear') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6"
                x-id="['nav-zone']"
                x-data="{
                    draggedItem: null,
                    draggedCategoryId: null,
                    draggedOver: null,
                    dragOverZone: false,
                    dragTarget: null,
                    itemIds: [],
                    
                    init() {
                        this.itemIds = this.getItemIds();
                        // Listen for category clicks
                        window.addEventListener('category-click', (event) => {
                            @this.call('addCategoryToNavigation', event.detail.id);
                        });
                    },
                    
                    handleDragStart(event, itemId) {
                        this.draggedItem = itemId;
                        this.draggedCategory = null;
                        this.draggedCategoryId = null;
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', 'item-' + itemId);
                        event.dataTransfer.setData('item-id', itemId.toString());
                        const card = event.currentTarget.closest('[data-item-id]');
                        if (card) {
                            card.style.opacity = '0.5';
                            card.style.transform = 'scale(0.98)';
                        }
                    },
                    
                    handleDragEnd(event) {
                        const card = event.currentTarget.closest('[data-item-id]');
                        if (card) {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }
                        this.draggedItem = null;
                        this.draggedOver = null;
                        this.dragOverZone = false;
                    },
                    
                    handleDragLeave(event) {
                        const relatedTarget = event.relatedTarget;
                        if (!relatedTarget || !event.currentTarget.contains(relatedTarget)) {
                            this.draggedOver = null;
                        }
                    },
                    
                    handleZoneDragOver(event) {
                        event.preventDefault();
                        event.dataTransfer.dropEffect = 'move';
                        
                        // Check if dragging a category or item
                        const isDraggingCategory = window.draggedCategoryId !== null && window.draggedCategoryId !== undefined;
                        const isDraggingItem = this.draggedItem !== null && this.draggedItem !== undefined;
                        
                        if (isDraggingCategory || isDraggingItem) {
                            this.dragOverZone = true;
                            // Store category ID for visual feedback
                            if (isDraggingCategory) {
                                this.draggedCategoryId = window.draggedCategoryId;
                            }
                        }
                    },
                    
                    handleZoneDragLeave(event) {
                        const relatedTarget = event.relatedTarget;
                        if (!relatedTarget || !event.currentTarget.contains(relatedTarget)) {
                            this.dragOverZone = false;
                            this.draggedCategoryId = null;
                        }
                    },
                    
                    handleZoneDrop(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        
                        // Check if dropping a category - read from dataTransfer or global variable
                        const categoryId = event.dataTransfer.getData('category-id') || window.draggedCategoryId || this.draggedCategoryId;
                        if (categoryId) {
                            @this.call('addCategoryToNavigation', parseInt(categoryId));
                            window.draggedCategoryId = null;
                            this.draggedCategoryId = null;
                            this.dragOverZone = false;
                            this.draggedItem = null;
                            return;
                        }
                        
                        // Handle navigation item drop on empty zone - make it a top-level item
                        // Try multiple sources for the item ID
                        let itemId = this.draggedItem;
                        if (!itemId) {
                            itemId = event.dataTransfer.getData('item-id');
                        }
                        if (!itemId) {
                            // Try parsing from text/plain
                            const textData = event.dataTransfer.getData('text/plain');
                            if (textData && textData.startsWith('item-')) {
                                itemId = parseInt(textData.replace('item-', ''));
                            }
                        }
                        
                        if (!itemId) {
                            this.draggedItem = null;
                            this.dragOverZone = false;
                            return;
                        }
                        
                        const itemIdInt = parseInt(itemId);
                        
                        // Make it a top-level item (remove parent)
                        @this.call('updateItemParent', itemIdInt, null);
                        
                        // Reorder items after a short delay to allow Livewire to update
                        setTimeout(() => {
                            this.itemIds = this.getItemIds();
                            const oldIndex = this.itemIds.indexOf(itemIdInt);
                            if (oldIndex !== -1) {
                                this.itemIds.splice(oldIndex, 1);
                                this.itemIds.push(itemIdInt);
                                @this.call('updateOrder', this.itemIds);
                            }
                        }, 200);
                        
                        this.draggedItem = null;
                        this.draggedOver = null;
                        this.dragOverZone = false;
                        this.dragTarget = null;
                    },
                    
                    handleItemDragOver(event, itemId) {
                        event.preventDefault();
                        event.stopPropagation(); // Prevent zone from handling this
                        event.dataTransfer.dropEffect = 'move';

                        // Allow category drops
                        if (window.draggedCategoryId || this.draggedCategoryId) {
                            return;
                        }

                        if (this.draggedItem && this.draggedItem !== itemId) {
                            this.draggedOver = itemId;
                            // Top/bottom edge of the row reorders as a sibling before/after
                            // it; the middle band nests it as a child of the target.
                            const rect = event.currentTarget.getBoundingClientRect();
                            const ratio = (event.clientY - rect.top) / rect.height;
                            const position = ratio < 0.3 ? 'before' : (ratio > 0.7 ? 'after' : 'child');
                            this.dragTarget = { id: itemId, position };
                            // Clear zone drag over state when dragging over an item
                            this.dragOverZone = false;
                        }
                    },

                    handleItemDrop(event, itemId) {
                        event.preventDefault();
                        event.stopPropagation();

                        // Check if dropping a category - read from dataTransfer or global variable
                        const categoryId = event.dataTransfer.getData('category-id') || window.draggedCategoryId || this.draggedCategoryId;
                        if (categoryId) {
                            @this.call('addCategoryToNavigation', parseInt(categoryId));
                            window.draggedCategoryId = null;
                            this.draggedCategoryId = null;
                            this.dragOverZone = false;
                            return;
                        }

                        // Handle navigation item drop
                        if (!this.draggedItem || this.draggedItem === itemId) {
                            return;
                        }

                        const position = (this.dragTarget && this.dragTarget.id === itemId) ? this.dragTarget.position : 'child';
                        @this.call('moveNavigationItem', this.draggedItem, itemId, position);

                        this.draggedItem = null;
                        this.draggedOver = null;
                        this.dragOverZone = false;
                        this.dragTarget = null;
                    },
                    
                    getItemIds() {
                        return Array.from(document.querySelectorAll('[data-item-id]')).map(card => 
                            parseInt(card.getAttribute('data-item-id'))
                        );
                    }
                }"
            >
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ __('Nav Links — Menu Items') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Content shown inside the "Nav Links" component above — drag and drop to reorder items') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $items->count() }} {{ __('items') }}
                            @if($search)
                                <span class="text-xs">({{ __('filtered') }})</span>
                            @endif
                        </div>
                        @if($items->count() > 0)
                            <flux:checkbox wire:model.live="selectAll" label="{{ __('Select All') }}" />
                        @endif
                    </div>
                </div>

                <div 
                    class="space-y-3 min-h-[400px] transition-all duration-200"
                    :class="{ 
                        'bg-blue-50/30 dark:bg-blue-900/10 rounded-lg p-4 border-2 border-dashed border-blue-300 dark:border-blue-600': dragOverZone && (draggedItem || draggedCategoryId),
                        'ring-2 ring-green-400 dark:ring-green-500': dragOverZone && draggedCategoryId,
                        'ring-2 ring-purple-400 dark:ring-purple-500': dragOverZone && draggedItem && !draggedCategoryId
                    }"
                    @dragover="handleZoneDragOver($event)"
                    @dragleave="handleZoneDragLeave($event)"
                    @drop="handleZoneDrop($event)"
                >
                    @forelse($items as $item)
                        <div
                            data-item-id="{{ $item->id }}"
                            class="group relative bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-4 transition-all duration-200 cursor-move hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 {{ $item->parent_id ? 'ml-6 border-l-4 border-l-purple-400 dark:border-l-purple-500' : '' }} {{ in_array($item->id, $selectedItems) ? 'ring-2 ring-blue-500 dark:ring-blue-400' : '' }}"
                            :class="{
                                'shadow-[inset_0_3px_0_0_#a855f7]': draggedOver == {{ $item->id }} && dragTarget?.position === 'before' && draggedItem != {{ $item->id }},
                                'shadow-[inset_0_-3px_0_0_#a855f7]': draggedOver == {{ $item->id }} && dragTarget?.position === 'after' && draggedItem != {{ $item->id }},
                                'bg-blue-50 dark:bg-blue-900/20 border-blue-400 dark:border-blue-500 border-l-4 ring-2 ring-purple-400 dark:ring-purple-500': draggedOver == {{ $item->id }} && dragTarget?.position === 'child' && draggedItem != {{ $item->id }},
                                'opacity-50 scale-95': draggedItem == {{ $item->id }}
                            }"
                            draggable="true"
                            @dragstart="handleDragStart($event, {{ $item->id }})"
                            @dragend="handleDragEnd($event)"
                            @dragover="handleItemDragOver($event, {{ $item->id }})"
                            @dragleave="handleDragLeave($event)"
                            @drop="handleItemDrop($event, {{ $item->id }})"
                        >
                            <div
                                x-show="draggedOver == {{ $item->id }} && draggedItem && draggedItem != {{ $item->id }}"
                                x-text="dragTarget?.position === 'child' ? '{{ __('Nest as child') }}' : (dragTarget?.position === 'before' ? '{{ __('Insert above') }}' : '{{ __('Insert below') }}')"
                                class="absolute -top-2.5 right-3 px-2 py-0.5 rounded-full text-[10px] font-semibold text-white bg-purple-500 shadow pointer-events-none z-10"
                                style="display: none;"
                            ></div>
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <!-- Checkbox for bulk selection -->
                                    <div class="pt-1">
                                        <flux:checkbox 
                                            wire:model.live="selectedItems" 
                                            value="{{ $item->id }}"
                                            class="cursor-pointer"
                                        />
                                    </div>
                                    
                                    <!-- Drag Handle -->
                                    <div class="pt-1 cursor-grab active:cursor-grabbing">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    
                                    @if($item->parent_id)
                                        <svg class="w-4 h-4 text-purple-500 dark:text-purple-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                    
                                    <!-- Icon -->
                                    @if($item->icon)
                                        <div class="flex-shrink-0 mt-1">
                                            <flux:icon name="{{ $item->icon }}" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                        </div>
                                    @endif
                                    
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $item->label_en }}</h4>
                                            <flux:badge size="sm" :variant="$item->is_active ? 'success' : 'danger'">
                                                {{ $item->is_active ? __('Active') : __('Inactive') }}
                                            </flux:badge>
                                            <flux:badge size="sm">
                                                {{ ucfirst($item->type) }}
                                            </flux:badge>
                                            @if($item->parent_id && $item->parent)
                                                <flux:badge size="sm" variant="info">
                                                    {{ __('Child of') }}: {{ $item->parent->label_en }}
                                                </flux:badge>
                                            @endif
                                            @if($item->children && $item->children->count() > 0)
                                                <flux:badge size="sm" variant="primary">
                                                    {{ $item->children->where('is_active', true)->count() }} {{ __('children') }}
                                                </flux:badge>
                                            @endif
                                        </div>
                                        
                                        @if($item->label_bn)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $item->label_bn }}</p>
                                        @endif
                                        
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                            <span class="truncate">{{ $item->url }}</span>
                                            @if($item->route_name)
                                                <span class="text-xs">({{ __('Route') }}: {{ $item->route_name }})</span>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                            {{ __('Order') }}: {{ $item->order }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:button wire:click="toggleStatus({{ $item->id }})" size="sm" variant="ghost" title="{{ $item->is_active ? __('Deactivate') : __('Activate') }}">
                                        @if($item->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        @endif
                                    </flux:button>
                                    <flux:button wire:click="openModal({{ $item->id }})" size="sm" variant="ghost" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $item->id }})" 
                                        wire:confirm="{{ __('Are you sure you want to delete this navigation item?') }}"
                                        size="sm" variant="danger" title="{{ __('Delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No navigation items') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Get started by adding a navigation item or category.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <style>
        .navigation-modal [x-dialog] > div {
            max-width: 42rem !important;
        }
    </style>
    <div class="navigation-modal">
        <flux:modal wire:model="showModal" name="navigation-item-modal">
        <form wire:submit="save">
            <flux:heading>{{ $editingId ? __('Edit Navigation Item') : __('Add Navigation Item') }}</flux:heading>

            <div class="space-y-6">
                <flux:field>
                    <flux:label>{{ __('Label (English)') }}</flux:label>
                    <flux:input wire:model="label_en" placeholder="{{ __('Enter label in English') }}" />
                    <flux:error name="label_en" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Label (Bangla)') }} <span class="text-gray-400">({{ __('Optional') }})</span></flux:label>
                    <flux:input wire:model="label_bn" placeholder="{{ __('Enter label in Bangla') }}" />
                    <flux:error name="label_bn" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Icon') }} <span class="text-gray-400">({{ __('Optional') }})</span></flux:label>
                    <flux:input wire:model="icon" placeholder="home, user, shopping-cart" />
                    <flux:error name="icon" />
                    <flux:description>{{ __('Enter Heroicon name (e.g., home, user, shopping-cart). See') }} <a href="https://heroicons.com/" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">heroicons.com</a> {{ __('for available icons') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Type') }}</flux:label>
                    <flux:select wire:model.live="type">
                        <option value="link">{{ __('Custom Link') }}</option>
                        <option value="section">{{ __('Section Anchor') }} (#section)</option>
                        <option value="route">{{ __('Laravel Route') }}</option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                @if($type === 'route')
                    <flux:field>
                        <flux:label>{{ __('Route Name') }}</flux:label>
                        <flux:input wire:model="route_name" placeholder="home, about, contact" />
                        <flux:error name="route_name" />
                        <flux:description>{{ __('Enter the Laravel route name (e.g., home, about)') }}</flux:description>
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ $type === 'section' ? __('Section ID') : ($type === 'route' ? __('Fallback URL') : __('URL')) }}</flux:label>
                    <flux:input wire:model="url" 
                        placeholder="{{ $type === 'section' ? '#features' : ($type === 'route' ? 'https://example.com' : 'https://example.com or /page') }}" />
                    <flux:error name="url" />
                    <flux:description>
                        @if($type === 'section')
                            {{ __('Enter section ID with # (e.g., #features, #testimonials)') }}
                        @elseif($type === 'route')
                            {{ __('Fallback URL if route doesn\'t exist') }}
                        @else
                            {{ __('Enter full URL or relative path') }}
                        @endif
                    </flux:description>
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:checkbox wire:model="is_active" label="{{ __('Active') }}" />
                        <flux:error name="is_active" />
                    </div>
                    <div>
                        <flux:checkbox wire:model="open_in_new_tab" label="{{ __('Open in New Tab') }}" />
                        <flux:error name="open_in_new_tab" />
                    </div>
                </div>

                <flux:field>
                    <flux:label>{{ __('Parent Item') }} <span class="text-gray-400">({{ __('Optional') }})</span></flux:label>
                    <flux:select wire:model="parent_id">
                        <option value="">{{ __('None (Top Level)') }}</option>
                        @foreach($parentOptions as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->label_en }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="parent_id" />
                    <flux:description>{{ __('Select a parent item to make this a child menu item') }}</flux:description>
                </flux:field>
            </div>

            <div class="flex gap-4">
                <flux:button type="submit" variant="primary">{{ $editingId ? __('Update') : __('Create') }}</flux:button>
                <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </form>
        </flux:modal>
    </div>
</div>
