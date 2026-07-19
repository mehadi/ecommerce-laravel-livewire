<div class="space-y-6">
    <x-admin.page-header :heading="__('Testimonials')" :description="__('Manage customer testimonials and reviews')">
        <flux:button wire:click="createTestimonial" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Testimonial') }}</span>
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
    <div class="grid gap-4 md:grid-cols-5">
        <x-admin.stat-card :label="__('Total Testimonials')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Active')" :value="$stats['active']" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Inactive')" :value="$stats['inactive']" tone="zinc">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Average Rating')" :value="$stats['average_rating'].' ★'" tone="amber">
            <x-slot:icon>
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('5 Star Ratings')" :value="$stats['by_rating'][5] ?? 0" tone="purple">
            <x-slot:icon>
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
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
        <x-admin.bulk-actions-bar :count="count($selectedItems)">
            <flux:button wire:click="bulkToggleStatus" size="sm" variant="ghost" wire:loading.attr="disabled" wire:target="bulkToggleStatus">
                {{ __('Toggle Status') }}
            </flux:button>
            <flux:button wire:click="bulkDelete"
                wire:confirm="{{ __('Are you sure you want to delete the selected testimonials?') }}"
                size="sm" variant="danger" wire:loading.attr="disabled" wire:target="bulkDelete">
                {{ __('Delete Selected') }}
            </flux:button>
        </x-admin.bulk-actions-bar>
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
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" aria-label="{{ __('Select all testimonials') }}" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Image') }}</th>
                    <x-admin.sortable-th field="name" :label="__('Name')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="location" :label="__('Location')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Content Preview') }}</th>
                    <x-admin.sortable-th field="rating" :label="__('Rating')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="order" :label="__('Order')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="is_active" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($testimonials as $testimonial)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors cursor-move"
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
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $testimonial->id }}" aria-label="{{ __('Select :name', ['name' => $testimonial->name]) }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($testimonial->image)
                                <img src="{{ asset('storage/'.$testimonial->image) }}" alt="{{ $testimonial->name }}"
                                    class="w-12 h-12 rounded-full object-cover border-2 border-zinc-200 dark:border-zinc-700">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700">
                                    <svg class="h-6 w-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                                <div class="text-zinc-900 dark:text-white font-medium">{{ $testimonial->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-400">
                            {{ $testimonial->location ?? __('Not specified') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 max-w-xs">
                                {{ \Illuminate\Support\Str::limit($testimonial->content_en ?? $testimonial->content_bn, 80) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-amber-400 fill-current' : 'text-zinc-300 dark:text-zinc-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="ml-1 text-xs text-zinc-500 dark:text-zinc-400">({{ $testimonial->rating }})</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-900 dark:text-white font-medium">{{ $testimonial->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:badge :variant="$testimonial->is_active ? 'success' : 'danger'">
                                    {{ $testimonial->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                                <button wire:click="toggleStatus({{ $testimonial->id }})"
                                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                    title="{{ $testimonial->is_active ? __('Deactivate') : __('Activate') }}"
                                    aria-label="{{ $testimonial->is_active ? __('Deactivate :name', ['name' => $testimonial->name]) : __('Activate :name', ['name' => $testimonial->name]) }}">
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
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this testimonial?') }}"
                                    wire:click="deleteTestimonial({{ $testimonial->id }})" size="sm">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>{{ __('Delete') }}</span>
                                    </span>
                                </x-admin.confirm-delete-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="9" :title="__('No testimonials found')" :description="__('Get started by creating your first testimonial.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button wire:click="createTestimonial" variant="primary" size="sm">
                            {{ __('Add New Testimonial') }}
                        </flux:button>
                    </x-admin.table-empty-state>
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
                    <flux:label badge="{{ __('Required') }}">{{ __('Name') }}</flux:label>
                    <flux:input required wire:model="name" placeholder="{{ __('Customer name') }}" />
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
                    <flux:label badge="{{ __('Required') }}">{{ __('Content (English)') }}</flux:label>
                    <flux:textarea required wire:model="content_en" rows="4" placeholder="{{ __('Customer testimonial in English') }}" />
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
                            <img src="{{ asset('storage/'.$current_image) }}" alt="Current image" class="w-24 h-24 rounded-full object-cover border border-zinc-200 dark:border-zinc-700">
                        </div>
                    @endif
                    <flux:input type="file" wire:model="image" accept="image/*" />
                    <div wire:loading wire:target="image" class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Uploading...') }}</div>
                    <flux:error name="image" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Upload a profile picture (max 2MB, recommended: square image 200x200px)') }}</p>
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Rating') }}</flux:label>
                        <flux:input required type="number" min="1" max="5" wire:model="rating" />
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

                <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Is Active') }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Active testimonials are displayed on the website') }}</p>
                    </div>
                    <flux:switch wire:model="is_active" />
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="storeTestimonial,updateTestimonial">
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
