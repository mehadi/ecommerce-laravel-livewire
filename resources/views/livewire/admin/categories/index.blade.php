<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Categories') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage and organize your product categories') }}
            </flux:text>
        </div>
        <flux:button wire:click="openModal" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Category') }}</span>
            </span>
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Categories') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active') }}</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Inactive') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-500 dark:text-zinc-400">{{ $stats['inactive'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                    <svg class="h-6 w-6 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Subcategories') }}</p>
                    <p class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['subcategories'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search categories...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterParent">
                <option value="">{{ __('All Parents') }}</option>
                <option value="none">{{ __('Main Categories') }}</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name_en }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterHasSubcategories">
                <option value="">{{ __('All') }}</option>
                <option value="yes">{{ __('Has Subcategories') }}</option>
                <option value="no">{{ __('No Subcategories') }}</option>
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
        <div class="flex items-center justify-between gap-4 rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                    {{ __(':count item(s) selected', ['count' => count($selectedItems)]) }}
                </span>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="bulkToggleStatus" size="sm" variant="ghost">
                    {{ __('Toggle Status') }}
                </flux:button>
                <flux:button wire:click="bulkDelete" 
                    wire:confirm="{{ __('Are you sure you want to delete the selected categories?') }}"
                    size="sm" variant="danger">
                    {{ __('Delete Selected') }}
                </flux:button>
            </div>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2 items-start">
            <!-- Table View -->
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('List View') }}</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Total') }}: {{ $categories->total() }}
                    </span>
                </div>
                
                <div class="mb-4">
                    <flux:field>
                        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search categories...') }}" />
                    </flux:field>
                </div>

                <div class="flex-1 flex flex-col min-h-0">
                    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow flex-1">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="bg-gray-50 dark:bg-zinc-800 sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Image') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                        wire:click="sortBy('name_en')">
                                        <div class="flex items-center gap-2">
                                            {{ __('Name') }}
                                            @if($sortField === 'name_en')
                                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Parent') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Products') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                        wire:click="sortBy('is_active')">
                                        <div class="flex items-center gap-2">
                                            {{ __('Status') }}
                                            @if($sortField === 'is_active')
                                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                        wire:click="sortBy('order')">
                                        <div class="flex items-center gap-2">
                                            {{ __('Order') }}
                                            @if($sortField === 'order')
                                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                                @forelse($categories as $category)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <flux:checkbox wire:model.live="selectedItems" value="{{ $category->id }}" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($category->image)
                                                <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name_en }}" 
                                                    class="h-10 w-10 rounded object-cover">
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded bg-zinc-100 dark:bg-zinc-800">
                                                    <svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2" @if($category->parent_id) style="padding-left: {{ ($category->depth - 1) * 16 }}px" @endif>
                                                @if($category->parent_id)
                                                    <span class="text-gray-400 dark:text-gray-500">└─</span>
                                                @endif
                                                <div>
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ $category->name_en }}</span>
                                                    @if($category->name_bn)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->name_bn }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            @if($category->parent)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                                    </svg>
                                                    {{ $category->parent->name_en }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">{{ __('Main Category') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $category->products_count ?? 0 }}</span>
                                                @if(($category->children_count ?? 0) > 0)
                                                    <flux:badge size="sm" variant="subtle" title="{{ __('Has :count subcategories', ['count' => $category->children_count]) }}">
                                                        {{ $category->children_count }} {{ __('sub') }}
                                                    </flux:badge>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <flux:badge :variant="$category->is_active ? 'success' : 'danger'">
                                                    {{ $category->is_active ? __('Active') : __('Inactive') }}
                                                </flux:badge>
                                                <button wire:click="toggleStatus({{ $category->id }})" 
                                                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                                    title="{{ $category->is_active ? __('Deactivate') : __('Activate') }}">
                                                    @if($category->is_active)
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $category->order }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <flux:button wire:click="openModal({{ $category->id }})" size="sm" variant="ghost">
                                                    <span class="inline-flex items-center gap-1.5">
                                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        <span>{{ __('Edit') }}</span>
                                                    </span>
                                                </flux:button>
                                                <flux:button wire:click="duplicate({{ $category->id }})" size="sm" variant="ghost">
                                                    <span class="inline-flex items-center gap-1.5">
                                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span>{{ __('Duplicate') }}</span>
                                                    </span>
                                                </flux:button>
                                                <flux:button wire:click="delete({{ $category->id }})" size="sm" variant="danger" 
                                                    wire:confirm="{{ __('Are you sure you want to delete this category?') }}">
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
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                <div class="text-center">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('No categories found') }}</p>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ __('Get started by creating your first category.') }}
                                                    </p>
                                                </div>
                                                <flux:button wire:click="openModal" variant="primary" size="sm">
                                                    {{ __('Add New Category') }}
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($categories->hasPages())
                        <div class="mt-4">
                            {{ $categories->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tree View -->
            <div class="flex flex-col h-full"
                x-data="{
                    draggedCategoryId: null,
                    draggedOverParentId: null,
                    draggedCategoryParentId: null,
                    
                    handleDragStart(event, categoryId, parentId) {
                        this.draggedCategoryId = categoryId;
                        this.draggedCategoryParentId = parentId;
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', categoryId.toString());
                        event.currentTarget.style.opacity = '0.5';
                        event.currentTarget.style.cursor = 'grabbing';
                    },
                    
                    handleDragEnd(event) {
                        event.currentTarget.style.opacity = '';
                        event.currentTarget.style.cursor = '';
                        this.draggedCategoryId = null;
                        this.draggedOverParentId = null;
                        this.draggedCategoryParentId = null;
                    },
                    
                    handleDragOver(event, parentId) {
                        event.preventDefault();
                        event.dataTransfer.dropEffect = 'move';
                        this.draggedOverParentId = parentId;
                    },
                    
                    handleDragLeave() {
                        this.draggedOverParentId = null;
                    },
                    
                    handleDrop(event, newParentId) {
                        event.preventDefault();
                        
                        if (this.draggedCategoryId === null) {
                            return;
                        }
                        
                        const categoryId = this.draggedCategoryId;
                        const currentParentId = this.draggedCategoryParentId;
                        
                        if (newParentId === currentParentId) {
                            this.handleDragEnd(event);
                            return;
                        }
                        
                        @this.call('updateCategoryParent', categoryId, newParentId);
                        
                        this.handleDragEnd(event);
                    },
                    
                    isDraggedOver(parentId) {
                        return this.draggedOverParentId === parentId && this.draggedCategoryId !== null;
                    },
                    
                    isDragging(categoryId) {
                        return this.draggedCategoryId === categoryId;
                    }
                }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Hierarchical View') }}</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        @php
                            $mainCount = $this->categoryTree->count();
                            $subCount = $stats['subcategories'];
                        @endphp
                        {{ $mainCount }} {{ __('main') }}, {{ $subCount }} {{ __('sub') }}
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">{{ __('Drag categories to reorganize - move subcategories between parents or make main categories into subcategories') }}</p>
                
                <div class="flex-1 flex flex-col min-h-0">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6 flex-1 overflow-y-auto">
                        <div class="space-y-2">
                        @forelse($this->categoryTree as $parentCategory)
                            @include('livewire.admin.categories.partials.tree-item', ['category' => $parentCategory, 'depth' => 0])
                        @empty
                            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span class="font-medium">{{ __('No categories found.') }}</span>
                                    <span class="text-sm">{{ __('Create your first category to get started.') }}</span>
                                </div>
                            </div>
                        @endforelse
                        
                        <!-- Drop zone for moving to no parent (main category) -->
                        <div 
                            class="mt-4 p-3 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-lg transition-colors text-center"
                            :class="{
                                'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20': isDraggedOver(null) && draggedCategoryId !== null && draggedCategoryParentId !== null
                            }"
                            @dragover.prevent="handleDragOver($event, null)"
                            @dragleave="handleDragLeave"
                            @drop.prevent="handleDrop($event, null)"
                            x-show="draggedCategoryId !== null && draggedCategoryParentId !== null"
                            x-transition
                        >
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Drop here to make it a main category') }}
                            </span>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($showModal)
            <flux:modal wire:model="showModal" name="category-modal" class="max-w-2xl">
                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <flux:heading>{{ $editingId ? __('Edit Category') : __('Add New Category') }}</flux:heading>
                            <flux:text size="sm" variant="subtle" class="mt-0.5">
                                {{ $editingId ? __('Update the details of this category') : __('Organize your catalog by creating a new category') }}
                            </flux:text>
                        </div>
                    </div>

                    <div class="max-h-[65vh] space-y-6 overflow-y-auto pr-1 -mr-1">
                        <flux:field>
                            <flux:label>{{ __('Parent Category') }}</flux:label>
                            <flux:select wire:model="parent_id">
                                <option value="">{{ __('Main Category (No Parent)') }}</option>
                                @foreach($this->parentCategories as $item)
                                    <option value="{{ $item['category']->id }}">
                                        {{ str_repeat('— ', $item['depth']) }}{{ $item['category']->name_en }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Leave empty to create a top-level category') }}</p>
                            <flux:error name="parent_id" />
                        </flux:field>

                        <flux:separator />

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Name (English)') }} *</flux:label>
                                <flux:input wire:model.live.debounce.300ms="name_en" placeholder="{{ __('e.g. Fresh Fruits') }}" />
                                <flux:error name="name_en" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Name (Bangla)') }}</flux:label>
                                <flux:input wire:model="name_bn" placeholder="{{ __('e.g. তাজা ফল') }}" />
                                <flux:error name="name_bn" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <div class="flex items-center justify-between">
                                <flux:label>{{ __('Slug') }} *</flux:label>
                                @if($name_en && !$editingId)
                                    <button type="button" wire:click="generateSlug" class="cursor-pointer text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('Regenerate') }}
                                    </button>
                                @endif
                            </div>
                            <flux:input wire:model.live.debounce.500ms="slug" placeholder="{{ __('url-friendly-identifier') }}" />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Auto-generated from the English name. Used in the category URL.') }}</p>
                            <div wire:loading.remove wire:target="checkSlugAvailability,updatedSlug,updatedNameEn,generateSlug" class="mt-1.5">
                                @if($slugAvailable === true)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ __('Available') }}
                                    </span>
                                @elseif($slugAvailable === false && $slug)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ __('Already taken') }}
                                    </span>
                                @endif
                            </div>
                            <div wire:loading wire:target="checkSlugAvailability,updatedSlug,updatedNameEn,generateSlug" class="mt-1.5">
                                <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    <svg class="h-3.5 w-3.5 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Checking availability...') }}
                                </span>
                            </div>
                            <flux:error name="slug" />
                        </flux:field>

                        <flux:separator />

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Description (English)') }}</flux:label>
                                <flux:textarea wire:model="description_en" rows="3" placeholder="{{ __('Short description shown to customers') }}" />
                                <flux:error name="description_en" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Description (Bangla)') }}</flux:label>
                                <flux:textarea wire:model="description_bn" rows="3" />
                                <flux:error name="description_bn" />
                            </flux:field>
                        </div>

                        <flux:separator />

                        <flux:field>
                            <flux:label>{{ __('Category Image') }}</flux:label>
                            @if($current_image || $image)
                                <div class="flex items-center gap-4 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                    <img src="{{ $image ? $image->temporaryUrl() : asset('storage/'.$current_image) }}"
                                        class="h-16 w-16 shrink-0 rounded-lg object-cover" alt="{{ __('Category image preview') }}">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ __('Current image') }}</p>
                                        <label class="mt-1 inline-flex cursor-pointer items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ __('Replace image') }}
                                            <input type="file" wire:model="image" accept="image/*" class="hidden">
                                        </label>
                                    </div>
                                    <button type="button" wire:click="removeImage" wire:confirm="{{ __('Remove this image?') }}"
                                        class="shrink-0 cursor-pointer text-zinc-400 transition-colors hover:text-red-600 dark:hover:text-red-400"
                                        title="{{ __('Remove image') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-300 p-6 text-center transition-colors hover:border-blue-400 dark:border-zinc-700 dark:hover:border-blue-500">
                                    <svg class="h-8 w-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Click to upload an image') }}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('PNG or JPG, up to 2MB') }}</span>
                                    <input type="file" wire:model="image" accept="image/*" class="hidden">
                                </label>
                            @endif
                            <div wire:loading wire:target="image" class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Uploading...') }}</div>
                            <flux:error name="image" />
                        </flux:field>

                        <flux:separator />

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Display Order') }}</flux:label>
                                <flux:input type="number" wire:model="order" min="0" />
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Lower numbers appear first') }}</p>
                                <flux:error name="order" />
                            </flux:field>

                            <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Active') }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Visible to customers') }}</p>
                                </div>
                                <flux:switch wire:model="is_active" />
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" class="cursor-pointer">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? __('Update Category') : __('Save Category') }}</span>
                            <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                        </flux:button>
                        <flux:button type="button" wire:click="closeModal" variant="ghost" class="cursor-pointer">{{ __('Cancel') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        @endif
</div>
