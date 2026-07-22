<div class="space-y-6">
    <x-admin.page-header :heading="__('Inventory')" :description="__('Track stock levels, adjust quantities, and review stock movement history')">
        <flux:button :href="route('admin.inventory.suggested-reorders')" variant="ghost" wire:navigate>
            {{ __('Suggested Reorders') }}
        </flux:button>
        <flux:button :href="route('admin.inventory.expiring-batches')" variant="ghost" wire:navigate>
            {{ __('Expiring Batches') }}
        </flux:button>
        @can('manage inventory settings')
            <flux:button wire:click="recomputeAbcClasses" variant="ghost" wire:loading.attr="disabled" wire:target="recomputeAbcClasses">
                {{ __('Recalculate ABC Classes') }}
            </flux:button>
            <flux:button wire:click="openThresholdModal" variant="ghost">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ __('Low Stock Settings') }}</span>
                </span>
            </flux:button>
        @endcan
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <x-admin.stat-card :label="__('Total SKUs')" :value="$stats['total_skus']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Total Units In Stock')" :value="number_format($stats['total_units'])" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0l-2 7H6l-2-7m16 0H4"></path>
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

        <x-admin.stat-card :label="__('Inventory Value')" :value="\App\Models\Setting::get('currency_symbol', '৳').number_format($stats['total_value'], 2)" tone="indigo">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or SKU...') }}" />
            </flux:field>
        </div>
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
        @if($activeWarehouses->count() > 1)
            <flux:field>
                <flux:select wire:model.live="filterWarehouse">
                    <option value="">{{ __('All Warehouses') }}</option>
                    @foreach($activeWarehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif
        <flux:field>
            <flux:select wire:model.live="perPage">
                <option value="10">10 {{ __('per page') }}</option>
                <option value="15">15 {{ __('per page') }}</option>
                <option value="25">25 {{ __('per page') }}</option>
                <option value="50">50 {{ __('per page') }}</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="w-8 px-4 py-3"></th>
                    <x-admin.sortable-th field="name_en" :label="__('Product')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Category') }}</th>
                    @if($hasAnyAbcClass)
                        <x-admin.sortable-th field="abc_class" :label="__('ABC')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    @endif
                    <x-admin.sortable-th field="stock" :label="__('Stock')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    @if($hasAnyReservation)
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Available') }}</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @php
                    $filterWarehouseId = $filterWarehouse !== '' ? (int) $filterWarehouse : null;
                @endphp
                @forelse($products as $product)
                    @php
                        $syncedStock = $filterWarehouseId ? $product->getStockForWarehouse($filterWarehouseId) : $product->getSyncedStock();
                        $syncedAvailable = $filterWarehouseId ? $product->getAvailableForWarehouse($filterWarehouseId) : $product->getSyncedAvailable();
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-4">
                            @if($product->hasAttributes())
                                <button type="button" wire:click="toggleExpand({{ $product->id }})" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" aria-label="{{ __('Toggle variants') }}">
                                    <svg class="h-4 w-4 transition-transform {{ $expandedProductId === $product->id ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $product->name_en }}</div>
                            @if($product->sku)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">SKU: {{ $product->sku }}</div>
                            @endif
                            @if($product->hasAttributes())
                                <flux:badge size="sm" variant="subtle" class="mt-1">{{ __(':count variants', ['count' => $product->productAttributes->count()]) }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $product->category?->name_en ?? __('No Category') }}
                        </td>
                        @if($hasAnyAbcClass)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->abc_class)
                                    <flux:badge size="sm" :variant="match($product->abc_class) { 'A' => 'success', 'B' => 'warning', default => 'subtle' }">
                                        {{ $product->abc_class }}
                                    </flux:badge>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">—</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $syncedStock }}</span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('units') }}</span>
                        </td>
                        @if($hasAnyReservation)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $syncedAvailable }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('units') }}</span>
                            </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($syncedStock <= 0)
                                <flux:badge variant="danger" size="sm">{{ __('Out of Stock') }}</flux:badge>
                            @elseif($product->isLowStock())
                                <flux:badge variant="warning" size="sm">{{ __('Low Stock') }}</flux:badge>
                            @else
                                <flux:badge variant="success" size="sm">{{ __('In Stock') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @can('adjust stock')
                                    <flux:button wire:click="openAdjustModal({{ $product->id }})" size="sm" variant="ghost">
                                        {{ __('Adjust') }}
                                    </flux:button>
                                @endcan
                                <flux:button wire:click="openHistoryModal({{ $product->id }})" size="sm" variant="ghost">
                                    {{ __('History') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @if($product->hasAttributes() && $expandedProductId === $product->id)
                        @foreach($product->productAttributes as $variant)
                            @php
                                $variantStock = $filterWarehouseId ? $variant->warehouseStocks->where('warehouse_id', $filterWarehouseId)->sum('stock') : $variant->stock;
                                $variantReserved = $filterWarehouseId ? $variant->warehouseStocks->where('warehouse_id', $filterWarehouseId)->sum('reserved') : $variant->warehouseStocks->sum('reserved');
                            @endphp
                            <tr class="bg-zinc-50/60 dark:bg-zinc-800/40">
                                <td class="px-4 py-3"></td>
                                <td class="px-6 py-3 pl-4 text-sm text-zinc-700 dark:text-zinc-300">
                                    <span class="text-zinc-400">&#8627;</span> {{ $variant->attribute_label }}
                                    @if($variant->sku)
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500">SKU: {{ $variant->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3"></td>
                                @if($hasAnyAbcClass)
                                    <td class="px-6 py-3"></td>
                                @endif
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $variantStock }} <span class="text-xs text-zinc-400">{{ __('units') }}</span>
                                </td>
                                @if($hasAnyReservation)
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-700 dark:text-zinc-300">
                                        {{ $variantStock - $variantReserved }} <span class="text-xs text-zinc-400">{{ __('units') }}</span>
                                    </td>
                                @endif
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($variantStock <= 0)
                                        <flux:badge variant="danger" size="sm">{{ __('Out of Stock') }}</flux:badge>
                                    @elseif($variantStock > 0 && $variantStock <= $product->lowStockThreshold())
                                        <flux:badge variant="warning" size="sm">{{ __('Low Stock') }}</flux:badge>
                                    @else
                                        <flux:badge variant="success" size="sm">{{ __('In Stock') }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-3"></td>
                            </tr>
                        @endforeach
                    @endif
                @empty
                    <x-admin.table-empty-state :colspan="6 + ($hasAnyReservation ? 1 : 0) + ($hasAnyAbcClass ? 1 : 0)" :title="__('No products found')" :description="__('Products will appear here once you add them to your catalog.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0l-2 7H6l-2-7m16 0H4"></path>
                            </svg>
                        </x-slot:icon>
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

    {{-- Adjust Stock Modal --}}
    @if($showAdjustModal && $adjustingProduct)
        <flux:modal wire:model="showAdjustModal" name="adjust-stock-modal" class="max-w-xl">
            <form wire:submit.prevent="adjustStock" class="space-y-6">
                <div>
                    <flux:heading>{{ __('Adjust Stock') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-0.5">{{ $adjustingProduct->name_en }}</flux:text>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @if($activeWarehouses->count() > 1)
                        <flux:field>
                            <flux:label>{{ __('Warehouse') }}</flux:label>
                            <flux:select wire:model.live="adjustWarehouseId">
                                @foreach($activeWarehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    @endif
                    <flux:field>
                        <flux:label>{{ __('Mode') }}</flux:label>
                        <flux:select wire:model.live="adjustMode">
                            <option value="set">{{ __('Set to exact quantity') }}</option>
                            <option value="add">{{ __('Add to current stock') }}</option>
                            <option value="remove">{{ __('Remove from current stock') }}</option>
                            <option value="reserve">{{ __('Reserve additional stock') }}</option>
                            <option value="release">{{ __('Release reserved stock') }}</option>
                        </flux:select>
                    </flux:field>
                </div>

                @php $showBatchUi = $adjustingProduct->tracks_batches && ! in_array($adjustMode, ['reserve', 'release']); @endphp

                @if($adjustingProduct->hasAttributes())
                    <div class="space-y-3">
                        @foreach($adjustingProduct->productAttributes as $variant)
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $variant->attribute_label }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Current stock: :stock', ['stock' => $variantCurrentStocks[$variant->id] ?? 0]) }}</div>
                                </div>
                                <flux:input type="number" min="0" wire:model="variantQuantities.{{ $variant->id }}" class="w-28" />
                            </div>
                        @endforeach
                    </div>
                @elseif($showBatchUi)
                    <div class="space-y-3">
                        <flux:text size="sm" variant="subtle">{{ __('Sorted soonest-expiring first (FEFO). Edit a batch\'s quantity, or add a new batch below.') }}</flux:text>
                        @foreach($batchRows as $index => $row)
                            <div wire:key="batch-{{ $index }}" class="grid grid-cols-12 gap-2 items-end rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                <div class="col-span-4">
                                    <flux:field>
                                        <flux:label>{{ __('Batch #') }}</flux:label>
                                        <flux:input wire:model="batchRows.{{ $index }}.batch_number" :disabled="!empty($row['id'])" placeholder="{{ __('e.g. LOT-2026-07') }}" />
                                    </flux:field>
                                </div>
                                <div class="col-span-3">
                                    <flux:field>
                                        <flux:label>{{ __('Quantity') }}</flux:label>
                                        <flux:input type="number" min="0" wire:model="batchRows.{{ $index }}.quantity" />
                                    </flux:field>
                                </div>
                                <div class="col-span-4">
                                    <flux:field>
                                        <flux:label>{{ __('Expires') }}</flux:label>
                                        <flux:input type="date" wire:model="batchRows.{{ $index }}.expires_at" :disabled="!empty($row['id'])" />
                                    </flux:field>
                                </div>
                                <div class="col-span-1 flex items-end h-full pb-1">
                                    @if(empty($row['id']) && count($batchRows) > 1)
                                        <flux:button type="button" wire:click="removeBatchRow({{ $index }})" size="sm" variant="danger">&times;</flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <flux:button type="button" wire:click="addBatchRow" size="sm" variant="ghost">{{ __('Add Another Batch') }}</flux:button>
                    </div>
                @else
                    <flux:field>
                        <flux:label>{{ in_array($adjustMode, ['reserve', 'release']) ? __('Reserved quantity') : __('Quantity') }}</flux:label>
                        <flux:input type="number" min="0" wire:model="adjustQuantity" />
                    </flux:field>
                    <flux:text size="sm" variant="subtle">{{ __('Current stock: :stock', ['stock' => $adjustCurrentStock]) }}</flux:text>
                @endif

                <flux:field>
                    <flux:label>{{ __('Reason') }}</flux:label>
                    <flux:textarea wire:model="adjustReason" rows="2" placeholder="{{ __('e.g. Damaged goods, stock recount, supplier restock...') }}" />
                    <flux:error name="adjustReason" />
                </flux:field>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" wire:click="closeAdjustModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="adjustStock">
                        {{ __('Save Adjustment') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    {{-- History Modal --}}
    @if($showHistoryModal)
        <flux:modal wire:model="showHistoryModal" name="stock-history-modal" class="max-w-3xl">
            <div class="space-y-4">
                <div>
                    <flux:heading>{{ __('Stock Movement History') }}</flux:heading>
                    @if($historyProduct)
                        <flux:text size="sm" variant="subtle" class="mt-0.5">{{ $historyProduct->name_en }}</flux:text>
                    @endif
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Date') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Type') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Variant') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Change') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Reason') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('By') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($historyMovements as $movement)
                                <tr>
                                    <td class="px-4 py-2 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $movement->changed_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <flux:badge size="sm" :variant="$movement->type->badgeColor()">
                                            {{ $movement->type->label() }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $movement->productAttribute?->attribute_label ?? __('Base product') }}</td>
                                    <td class="px-4 py-2 text-sm whitespace-nowrap">
                                        <span class="{{ $movement->quantity_delta >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                                            {{ $movement->quantity_delta >= 0 ? '+' : '' }}{{ $movement->quantity_delta }}
                                        </span>
                                        <span class="text-xs text-zinc-400">({{ $movement->quantity_before }} &rarr; {{ $movement->quantity_after }})</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $movement->reason ?? '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $movement->changedBy?->name ?? __('System') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('No stock movements recorded yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="closeHistoryModal" variant="ghost">{{ __('Close') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    {{-- Low Stock Threshold Modal --}}
    @if($showThresholdModal)
        <flux:modal wire:model="showThresholdModal" name="threshold-modal" class="max-w-md">
            <form wire:submit.prevent="saveThreshold" class="space-y-6">
                <div>
                    <flux:heading>{{ __('Low Stock Settings') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-0.5">
                        {{ __('Products at or below this quantity are flagged as low stock, store-wide. Individual products can override this from their edit page.') }}
                    </flux:text>
                </div>

                <flux:field>
                    <flux:label>{{ __('Default low stock threshold') }}</flux:label>
                    <flux:input type="number" min="1" wire:model="lowStockThresholdSetting" />
                    <flux:error name="lowStockThresholdSetting" />
                </flux:field>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" wire:click="closeThresholdModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
