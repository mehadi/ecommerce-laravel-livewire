<div class="space-y-6">
    <x-admin.page-header heading="{{ __('Landing Page Sections') }}" description="{{ __('Manage sections displayed on the landing page') }}">
        <flux:button wire:click="editSection(null)" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Create Section') }}</span>
            </span>
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <x-admin.stat-card label="{{ __('Total Sections') }}" value="{{ $stats['total'] }}" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card label="{{ __('Active') }}" value="{{ $stats['active'] }}" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card label="{{ __('Inactive') }}" value="{{ $stats['inactive'] }}" tone="zinc">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card label="{{ __('Types') }}" value="{{ count($stats['by_type']) }}" tone="purple">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by title or content...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterType">
                <option value="">{{ __('All Types') }}</option>
                @foreach($this->sectionTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="perPage">
                <option value="10">10 {{ __('per page') }}</option>
                <option value="15">15 {{ __('per page') }}</option>
                <option value="25">25 {{ __('per page') }}</option>
                <option value="50">50 {{ __('per page') }}</option>
            </flux:select>
        </flux:field>
    </div>

    {{-- Bulk Actions --}}
    @if(count($selectedItems) > 0)
        <x-admin.bulk-actions-bar :count="count($selectedItems)">
            <flux:button wire:click="bulkToggleStatus" size="sm" variant="ghost" wire:loading.attr="disabled" wire:target="bulkToggleStatus">
                {{ __('Toggle Status') }}
            </flux:button>
            <flux:button wire:click="bulkDelete"
                wire:confirm="{{ __('Are you sure you want to permanently delete the selected sections? This cannot be undone.') }}"
                size="sm" variant="danger" wire:loading.attr="disabled" wire:target="bulkDelete">
                {{ __('Delete Selected') }}
            </flux:button>
        </x-admin.bulk-actions-bar>
    @endif

    @unless($filterType)
        <p class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ __('Filter by a section type above to enable drag-and-drop manual reordering.') }}
        </p>
    @endunless

    <div class="overflow-x-auto rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
        x-data="{
            draggedItem: null,
            draggedOver: null,
            filterType: @entangle('filterType'),
            sectionIds: [],
            
            init() {
                this.$watch('filterType', () => {
                    this.$nextTick(() => {
                        this.sectionIds = this.getSectionIds();
                    });
                });
                this.sectionIds = this.getSectionIds();
            },
            
            handleDragStart(event, sectionId) {
                if (!this.filterType) return;
                this.draggedItem = sectionId;
                event.dataTransfer.effectAllowed = 'move';
                event.target.style.opacity = '0.5';
            },
            
            handleDragEnd(event) {
                event.target.style.opacity = '1';
                this.draggedItem = null;
                this.draggedOver = null;
            },
            
            handleDragOver(event, sectionId) {
                if (!this.filterType) return;
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                this.draggedOver = sectionId;
            },
            
            handleDragLeave() {
                this.draggedOver = null;
            },
            
            handleDrop(event, sectionId) {
                if (!this.filterType) return;
                event.preventDefault();
                
                if (this.draggedItem === sectionId) {
                    return;
                }
                
                // Recalculate sectionIds from current DOM order
                this.sectionIds = this.getSectionIds();
                
                const oldIndex = this.sectionIds.indexOf(this.draggedItem);
                const newIndex = this.sectionIds.indexOf(sectionId);
                
                // Remove from old position
                this.sectionIds.splice(oldIndex, 1);
                // Insert at new position
                this.sectionIds.splice(newIndex, 0, this.draggedItem);
                
                // Update order via Livewire
                @this.call('updateOrder', this.sectionIds);
                
                this.draggedItem = null;
                this.draggedOver = null;
            },
            
            getSectionIds() {
                return Array.from(document.querySelectorAll('tbody tr[data-section-id]')).map(row => 
                    parseInt(row.getAttribute('data-section-id'))
                );
            }
        }"
    >
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" aria-label="{{ __('Select all') }}" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Image') }}</th>
                    <x-admin.sortable-th field="type" label="{{ __('Type') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="title_en" label="{{ __('Title') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Content Preview') }}</th>
                    <x-admin.sortable-th field="order" label="{{ __('Order') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="is_active" label="{{ __('Status') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($sections as $section)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        data-section-id="{{ $section->id }}"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': filterType && draggedOver === {{ $section->id }}, 'cursor-move': filterType }"
                        :draggable="filterType ? 'true' : 'false'"
                        @dragstart="handleDragStart($event, {{ $section->id }})"
                        @dragend="handleDragEnd($event)"
                        @dragover="handleDragOver($event, {{ $section->id }})"
                        @dragleave="handleDragLeave()"
                        @drop="handleDrop($event, {{ $section->id }})"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $section->id }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($section->image)
                                <img src="{{ asset('storage/'.$section->image) }}" alt="{{ $section->title_en ?? $section->title_bn ?? 'Section' }}" 
                                    class="h-12 w-12 rounded object-cover border border-zinc-200 dark:border-zinc-700">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded bg-zinc-100 dark:bg-zinc-800">
                                    <svg class="h-6 w-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <template x-if="filterType">
                                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                </template>
                                <flux:badge variant="subtle" size="sm">
                                    {{ $this->sectionTypes[$section->type] ?? ucfirst($section->type) }}
                                </flux:badge>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-zinc-900 dark:text-white font-medium">
                                {{ $section->title_en ?? $section->title_bn ?? __('(No Title)') }}
                            </div>
                            @if($section->title_en && $section->title_bn)
                                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $section->title_bn }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($section->content_en || $section->content_bn)
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 max-w-xs">
                                    {{ \Illuminate\Support\Str::limit($section->content_en ?? $section->content_bn, 60) }}
                                </div>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500 text-sm">{{ __('No content') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-900 dark:text-white font-medium">{{ $section->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:badge :variant="$section->is_active ? 'success' : 'danger'">
                                    {{ $section->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                                <flux:button wire:click="toggleStatus({{ $section->id }})"
                                    size="sm" variant="ghost"
                                    aria-label="{{ $section->is_active ? __('Deactivate section') : __('Activate section') }}"
                                    title="{{ $section->is_active ? __('Deactivate') : __('Activate') }}">
                                    @if($section->is_active)
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </flux:button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="editSection({{ $section->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                <flux:button wire:click="duplicateSection({{ $section->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ __('Duplicate') }}</span>
                                    </span>
                                </flux:button>
                                <flux:button wire:click="deleteSection({{ $section->id }})" size="sm" variant="danger" 
                                    wire:confirm="{{ __('Are you sure you want to permanently delete this section? This cannot be undone.') }}">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>{{ __('Delete') }}</span>
                                    </span>
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="8" title="{{ __('No sections found') }}" description="{{ __('Get started by creating your first section.') }}">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button wire:click="editSection(null)" variant="primary" size="sm">
                            {{ __('Create Section') }}
                        </flux:button>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sections->hasPages())
        <div class="mt-4">
            {{ $sections->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="section-modal">
            <form wire:submit.prevent="saveSection" class="space-y-6">
                <flux:heading>{{ $editingSectionId ? __('Edit Section') : __('Create New Section') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Section Type') }} *</flux:label>
                    @if($editingSectionId)
                        <flux:input wire:model="type" readonly />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Section type cannot be changed after creation') }}</p>
                    @else
                        <flux:select wire:model="type" required>
                            <option value="">{{ __('Select Section Type') }}</option>
                            @foreach($this->sectionTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Choose the type of section you want to create') }}</p>
                    @endif
                    <flux:error name="type" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Title (English)') }}</flux:label>
                        <flux:input wire:model="title_en" placeholder="{{ __('Section title in English') }}" />
                        <flux:error name="title_en" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Title (Bangla)') }}</flux:label>
                        <flux:input wire:model="title_bn" placeholder="{{ __('Section title in Bangla') }}" />
                        <flux:error name="title_bn" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Content (English)') }}</flux:label>
                        <flux:textarea wire:model="content_en" rows="4" placeholder="{{ __('Section content in English') }}" />
                        <flux:error name="content_en" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Content (Bangla)') }}</flux:label>
                        <flux:textarea wire:model="content_bn" rows="4" placeholder="{{ __('Section content in Bangla') }}" />
                        <flux:error name="content_bn" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Section Image') }}</flux:label>
                    @if($current_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$current_image) }}" alt="Current image" class="w-32 h-32 object-cover rounded border border-zinc-200 dark:border-zinc-700">
                        </div>
                    @endif
                    <flux:input type="file" wire:model="image" accept="image/*" />
                    <flux:error name="image" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Upload an image for this section (max 1MB, recommended: 1200x600px)') }}</p>
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Display Order') }}</flux:label>
                        <flux:input type="number" wire:model="order" min="0" />
                        <flux:error name="order" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Lower numbers appear first on the landing page') }}</p>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Section Status') }}</flux:label>
                        <div class="mt-2">
                            <flux:switch wire:model="is_active" label="{{ __('Active (visible on landing page)') }}" />
                        </div>
                        <flux:error name="is_active" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Inactive sections are hidden from the landing page') }}</p>
                    </flux:field>
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveSection">
                            {{ $editingSectionId ? __('Update Section') : __('Create Section') }}
                        </span>
                        <span wire:loading wire:target="saveSection">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
