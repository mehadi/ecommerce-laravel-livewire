<div class="space-y-6">
    <x-admin.page-header :heading="__('Products')" :description="__('Manage your product catalog and inventory')">
        <flux:button :href="route('admin.products.create')" wire:navigate variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Product') }}</span>
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
    <div class="grid gap-4 md:grid-cols-4 lg:grid-cols-7">
        <x-admin.stat-card :label="__('Total Products')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
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

        <x-admin.stat-card :label="__('Featured')" :value="$stats['featured']" tone="purple">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Low Stock')" :value="$stats['low_stock']" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Out of Stock')" :value="$stats['out_of_stock']" tone="red">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Total Value')" :value="\App\Models\Setting::get('currency_symbol', '৳').number_format($stats['total_value'], 2)" tone="indigo">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search products...') }}" />
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
            <flux:select wire:model.live="filterCategory">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name_en }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterStock">
                <option value="">{{ __('All Stock') }}</option>
                <option value="in_stock">{{ __('In Stock') }}</option>
                <option value="low_stock">{{ __('Low Stock') }}</option>
                <option value="out_of_stock">{{ __('Out of Stock') }}</option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterFeatured">
                <option value="">{{ __('All') }}</option>
                <option value="featured">{{ __('Featured') }}</option>
                <option value="not_featured">{{ __('Not Featured') }}</option>
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
            <flux:button wire:click="bulkToggleFeatured" size="sm" variant="ghost" wire:loading.attr="disabled" wire:target="bulkToggleFeatured">
                {{ __('Toggle Featured') }}
            </flux:button>
            <flux:button wire:click="bulkDelete"
                wire:confirm="{{ __('Are you sure you want to delete the selected products?') }}"
                size="sm" variant="danger" wire:loading.attr="disabled" wire:target="bulkDelete">
                {{ __('Delete Selected') }}
            </flux:button>
        </x-admin.bulk-actions-bar>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" aria-label="{{ __('Select all products') }}" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Image') }}</th>
                    <x-admin.sortable-th field="name_en" :label="__('Name')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Category') }}</th>
                    <x-admin.sortable-th field="price" :label="__('Price')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="stock" :label="__('Stock')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="is_active" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="created_at" :label="__('Created')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($products as $product)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $product->id }}" aria-label="{{ __('Select :name', ['name' => $product->name_en]) }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->primary_image)
                                <img src="{{ asset('storage/'.$product->primary_image) }}" alt="{{ $product->name_en }}"
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
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $product->name_en }}</span>
                                    @if($product->is_featured)
                                        <flux:badge size="sm" variant="warning">
                                            <svg class="h-3 w-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ __('Featured') }}
                                        </flux:badge>
                                    @endif
                                </div>
                                @if($product->sku)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">SKU: {{ $product->sku }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->category)
                                <span class="inline-flex items-center gap-1 text-zinc-900 dark:text-white">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $product->category->name_en }}
                                </span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">{{ __('No Category') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                @php
                                    $syncedPrice = $product->getSyncedPrice();
                                    $syncedComparePrice = $product->getSyncedCompareAtPrice();
                                    $maxPrice = $product->getMaxPrice();
                                @endphp
                                @if($product->hasAttributes() && $syncedPrice != $maxPrice)
                                    <span class="text-zinc-900 dark:text-white font-medium">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($syncedPrice, 2) }} - {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($maxPrice, 2) }}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Attributes') }}</span>
                                @else
                                    <span class="text-zinc-900 dark:text-white font-medium">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($syncedPrice, 2) }}</span>
                                @endif
                                @if($syncedComparePrice && $syncedComparePrice > $syncedPrice)
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 line-through">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($syncedComparePrice, 2) }}</span>
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                        {{ round((($syncedComparePrice - $syncedPrice) / $syncedComparePrice) * 100) }}% {{ __('off') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @php
                                    $syncedStock = $product->getSyncedStock();
                                @endphp
                                @if($product->hasAttributes())
                                    <div class="flex flex-col">
                                        @if($syncedStock <= 0)
                                            <flux:badge variant="danger" size="sm">{{ __('Out of Stock') }}</flux:badge>
                                        @elseif($syncedStock <= 10)
                                            <flux:badge variant="warning" size="sm">{{ $syncedStock }} {{ __('left') }}</flux:badge>
                                        @else
                                            <span class="text-zinc-900 dark:text-white font-medium">{{ $syncedStock }}</span>
                                        @endif
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ __('Total') }}</span>
                                    </div>
                                @else
                                    @if($syncedStock <= 0)
                                        <flux:badge variant="danger" size="sm">{{ __('Out of Stock') }}</flux:badge>
                                    @elseif($syncedStock <= 10)
                                        <flux:badge variant="warning" size="sm">{{ $syncedStock }} {{ __('left') }}</flux:badge>
                                    @else
                                        <span class="text-zinc-900 dark:text-white font-medium">{{ $syncedStock }}</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:badge :variant="$product->is_active ? 'success' : 'danger'">
                                    {{ $product->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                                <button wire:click="toggleStatus({{ $product->id }})"
                                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                    title="{{ $product->is_active ? __('Deactivate') : __('Activate') }}"
                                    aria-label="{{ $product->is_active ? __('Deactivate :name', ['name' => $product->name_en]) : __('Activate :name', ['name' => $product->name_en]) }}">
                                    @if($product->is_active)
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
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $product->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button :href="route('admin.products.edit', $product)" wire:navigate size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                <x-admin.icon-button
                                    :aria-label="$product->is_featured ? __('Remove :name from Featured', ['name' => $product->name_en]) : __('Add :name to Featured', ['name' => $product->name_en])"
                                    wire:click="toggleFeatured({{ $product->id }})" size="sm" variant="ghost">
                                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                </x-admin.icon-button>
                                <x-admin.icon-button :aria-label="__('Duplicate :name', ['name' => $product->name_en])"
                                    wire:click="duplicateProduct({{ $product->id }})" size="sm" variant="ghost">
                                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </x-admin.icon-button>
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this product?') }}"
                                    wire:click="deleteProduct({{ $product->id }})" size="sm"
                                    aria-label="{{ __('Delete :name', ['name' => $product->name_en]) }}"
                                    title="{{ __('Delete Product') }}">
                                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </x-admin.confirm-delete-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="9" :title="__('No products found')" :description="__('Get started by creating your first product.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button :href="route('admin.products.create')" wire:navigate variant="primary" size="sm">
                            {{ __('Add New Product') }}
                        </flux:button>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
