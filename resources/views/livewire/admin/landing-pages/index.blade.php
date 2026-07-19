<div class="space-y-6">
    <x-admin.page-header heading="{{ __('Landing Pages') }}" description="{{ __('Manage and configure your landing pages') }}">
        <flux:button :href="route('admin.landing-pages.create')" wire:navigate variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Create Landing Page') }}</span>
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
    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.stat-card label="{{ __('Total Pages') }}" value="{{ $stats['total'] }}" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search landing pages...') }}" />
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
            <flux:select wire:model.live="filterProduct">
                <option value="">{{ __('All Products') }}</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name_en }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="perPage">
                <option value="10">10 {{ __('per page') }}</option>
                <option value="25">25 {{ __('per page') }}</option>
                <option value="50">50 {{ __('per page') }}</option>
                <option value="100">100 {{ __('per page') }}</option>
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
                wire:confirm="{{ __('Are you sure you want to permanently delete the selected landing pages? This cannot be undone.') }}"
                size="sm" variant="danger" wire:loading.attr="disabled" wire:target="bulkDelete">
                {{ __('Delete Selected') }}
            </flux:button>
        </x-admin.bulk-actions-bar>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" aria-label="{{ __('Select all') }}" />
                    </th>
                    <x-admin.sortable-th field="name" label="{{ __('Name') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Slug') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Product') }}</th>
                    <x-admin.sortable-th field="is_active" label="{{ __('Status') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="created_at" label="{{ __('Created') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($landingPages as $landingPage)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $landingPage->id }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $landingPage->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="text-sm text-zinc-600 dark:text-zinc-400">/lp/{{ $landingPage->slug }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-900 dark:text-white">
                            @if($landingPage->product)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    {{ $landingPage->product->name }}
                                </span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">{{ __('No Product') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:badge :variant="$landingPage->is_active ? 'success' : 'danger'">
                                    {{ $landingPage->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                                <flux:button wire:click="toggleStatus({{ $landingPage->id }})"
                                    size="sm" variant="ghost"
                                    aria-label="{{ $landingPage->is_active ? __('Deactivate landing page') : __('Activate landing page') }}"
                                    title="{{ $landingPage->is_active ? __('Deactivate') : __('Activate') }}">
                                    @if($landingPage->is_active)
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
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                            <div class="flex flex-col">
                                <span>{{ $landingPage->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-zinc-400">{{ $landingPage->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <flux:button :href="route('admin.landing-pages.edit', $landingPage)" wire:navigate size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                @if($landingPage->is_active)
                                    <flux:button :href="route('landing-page', $landingPage->slug)" target="_blank" rel="noopener" size="sm" variant="ghost">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>{{ __('View') }}</span>
                                        </span>
                                    </flux:button>
                                @endif
                                <flux:button wire:click="duplicate({{ $landingPage->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ __('Duplicate') }}</span>
                                    </span>
                                </flux:button>
                                <flux:button wire:click="deleteLandingPage({{ $landingPage->id }})" size="sm" variant="danger"
                                    wire:confirm="{{ __('Are you sure you want to permanently delete the landing page \':name\'? This cannot be undone.', ['name' => $landingPage->name]) }}">
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
                    <x-admin.table-empty-state colspan="7" title="{{ __('No landing pages found') }}" description="{{ __('Get started by creating a new landing page.') }}">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button :href="route('admin.landing-pages.create')" wire:navigate variant="primary" size="sm">
                            {{ __('Create Landing Page') }}
                        </flux:button>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($landingPages->hasPages())
        <div class="mt-4">
            {{ $landingPages->links() }}
        </div>
    @endif
</div>
