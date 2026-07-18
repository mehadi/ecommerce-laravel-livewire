<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Testimonials') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage customer testimonials and reviews') }}
            </flux:text>
        </div>
        <flux:button wire:click="createTestimonial" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Testimonial') }}</span>
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
    <div class="grid gap-4 md:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Testimonials') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Average Rating') }}</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $stats['average_rating'] }}
                        <span class="text-sm text-amber-500">★</span>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('5 Star Ratings') }}</p>
                    <p class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['by_rating'][5] ?? 0 }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name, location, or content...') }}" />
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
            <flux:select wire:model.live="filterRating">
                <option value="">{{ __('All Ratings') }}</option>
                <option value="5">5 {{ __('Stars') }}</option>
                <option value="4">4 {{ __('Stars') }}</option>
                <option value="3">3 {{ __('Stars') }}</option>
                <option value="2">2 {{ __('Stars') }}</option>
                <option value="1">1 {{ __('Star') }}</option>
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
                    wire:confirm="{{ __('Are you sure you want to delete the selected testimonials?') }}"
                    size="sm" variant="danger">
                    {{ __('Delete Selected') }}
                </flux:button>
            </div>
        </div>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow"
        x-data="{
            draggedItem: null,
            draggedOver: null,
            testimonialIds: [],
            
            init() {
                this.testimonialIds = this.getTestimonialIds();
            },
            
            getTestimonialIds() {
                return Array.from(document.querySelectorAll('tbody tr[data-testimonial-id]')).map(row => 
                    parseInt(row.getAttribute('data-testimonial-id'))
                );
            },
            
            handleDragStart(event, testimonialId) {
                this.draggedItem = testimonialId;
                event.dataTransfer.effectAllowed = 'move';
                event.target.style.opacity = '0.5';
            },
            
            handleDragEnd(event) {
                event.target.style.opacity = '1';
                this.draggedItem = null;
                this.draggedOver = null;
            },
            
            handleDragOver(event, testimonialId) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                this.draggedOver = testimonialId;
            },
            
            handleDragLeave() {
                this.draggedOver = null;
            },
            
            handleDrop(event, testimonialId) {
                event.preventDefault();
                
                if (this.draggedItem === testimonialId) {
                    return;
                }
                
                // Recalculate testimonialIds from current DOM order
                this.testimonialIds = this.getTestimonialIds();
                
                const oldIndex = this.testimonialIds.indexOf(this.draggedItem);
                const newIndex = this.testimonialIds.indexOf(testimonialId);
                
                // Remove from old position
                this.testimonialIds.splice(oldIndex, 1);
                // Insert at new position
                this.testimonialIds.splice(newIndex, 0, this.draggedItem);
                
                // Update order via Livewire
                @this.call('updateOrder', this.testimonialIds);
                
                this.draggedItem = null;
                this.draggedOver = null;
            }
        }"
    >
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Image') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('name')">
                        <div class="flex items-center gap-2">
                            {{ __('Name') }}
                            @if($sortField === 'name')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('location')">
                        <div class="flex items-center gap-2">
                            {{ __('Location') }}
                            @if($sortField === 'location')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Content Preview') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('rating')">
                        <div class="flex items-center gap-2">
                            {{ __('Rating') }}
                            @if($sortField === 'rating')
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($testimonials as $testimonial)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors cursor-move"
                        data-testimonial-id="{{ $testimonial->id }}"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': draggedOver === {{ $testimonial->id }} }"
                        draggable="true"
                        @dragstart="handleDragStart($event, {{ $testimonial->id }})"
                        @dragend="handleDragEnd($event)"
                        @dragover="handleDragOver($event, {{ $testimonial->id }})"
                        @dragleave="handleDragLeave()"
                        @drop="handleDrop($event, {{ $testimonial->id }})"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $testimonial->id }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($testimonial->image)
                                <img src="{{ asset('storage/'.$testimonial->image) }}" alt="{{ $testimonial->name }}" 
                                    class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-zinc-700">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-700">
                                    <svg class="h-6 w-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                                <div class="text-gray-900 dark:text-white font-medium">{{ $testimonial->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $testimonial->location ?? __('Not specified') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                {{ \Illuminate\Support\Str::limit($testimonial->content_en ?? $testimonial->content_bn, 80) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-yellow-400 fill-current' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ $testimonial->rating }})</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-medium">{{ $testimonial->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:badge :variant="$testimonial->is_active ? 'success' : 'danger'">
                                    {{ $testimonial->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                                <button wire:click="toggleStatus({{ $testimonial->id }})" 
                                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                    title="{{ $testimonial->is_active ? __('Deactivate') : __('Activate') }}">
                                    @if($testimonial->is_active)
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="editTestimonial({{ $testimonial->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                <flux:button wire:click="duplicateTestimonial({{ $testimonial->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ __('Duplicate') }}</span>
                                    </span>
                                </flux:button>
                                <flux:button wire:click="deleteTestimonial({{ $testimonial->id }})" size="sm" variant="danger" 
                                    wire:confirm="{{ __('Are you sure you want to delete this testimonial?') }}">
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
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('No testimonials found') }}</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Get started by creating your first testimonial.') }}
                                    </p>
                                </div>
                                <flux:button wire:click="createTestimonial" variant="primary" size="sm">
                                    {{ __('Add New Testimonial') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($testimonials->hasPages())
        <div class="mt-4">
            {{ $testimonials->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="testimonial-modal">
            <form wire:submit.prevent="{{ $editingId ? 'updateTestimonial' : 'storeTestimonial' }}" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Testimonial') : __('Add New Testimonial') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model="name" placeholder="{{ __('Customer name') }}" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('The name of the person giving the testimonial') }}</p>
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Location') }}</flux:label>
                    <flux:input wire:model="location" placeholder="{{ __('e.g., Dhaka, Bangladesh') }}" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional location of the customer') }}</p>
                    <flux:error name="location" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Content (English)') }} *</flux:label>
                    <flux:textarea wire:model="content_en" rows="4" placeholder="{{ __('Customer testimonial in English') }}" />
                    <flux:error name="content_en" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Content (Bangla)') }}</flux:label>
                    <flux:textarea wire:model="content_bn" rows="4" placeholder="{{ __('Customer testimonial in Bangla') }}" />
                    <flux:error name="content_bn" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Customer Image') }}</flux:label>
                    @if($current_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$current_image) }}" alt="Current image" class="w-24 h-24 rounded-full object-cover border border-gray-200 dark:border-zinc-700">
                        </div>
                    @endif
                    <flux:input type="file" wire:model="image" accept="image/*" />
                    <flux:error name="image" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Upload a profile picture (max 2MB, recommended: square image 200x200px)') }}</p>
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Rating') }} *</flux:label>
                        <flux:input type="number" min="1" max="5" wire:model="rating" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Customer rating from 1 to 5 stars') }}</p>
                        <flux:error name="rating" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Display Order') }}</flux:label>
                        <flux:input type="number" wire:model="order" min="0" />
                        <flux:error name="order" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Lower numbers appear first') }}</p>
                    </flux:field>
                </div>

                <flux:checkbox wire:model="is_active" label="{{ __('Is Active') }}" />
                <p class="text-xs text-zinc-500 dark:text-zinc-400 -mt-4">{{ __('Active testimonials are displayed on the website') }}</p>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="storeTestimonial,updateTestimonial">
                            {{ $editingId ? __('Update Testimonial') : __('Save Testimonial') }}
                        </span>
                        <span wire:loading wire:target="storeTestimonial,updateTestimonial">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
