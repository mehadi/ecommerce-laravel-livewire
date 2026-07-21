<div class="space-y-6">
    <x-admin.page-header :heading="__('Purchase Orders')" :description="__('Order and receive stock from suppliers')">
        <flux:button :href="route('admin.purchase-orders.create')" variant="primary" wire:navigate>
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('New Purchase Order') }}</span>
            </span>
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="grid gap-4 md:grid-cols-4">
        <x-admin.stat-card :label="__('Total Orders')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Open')" :value="$stats['open']" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Received')" :value="$stats['received']" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Cancelled')" :value="$stats['cancelled']" tone="red">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by order number or supplier...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="ordered">{{ __('Ordered') }}</option>
                <option value="partially_received">{{ __('Partially Received') }}</option>
                <option value="received">{{ __('Received') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
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

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Order #') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Supplier') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Items') }}</th>
                    <x-admin.sortable-th field="status" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="created_at" :label="__('Created')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($orders as $order)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-zinc-700 dark:text-zinc-300">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ __(':count item(s)', ['count' => $order->items->count()]) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge size="sm" :variant="match($order->status) { 'received' => 'success', 'cancelled' => 'danger', 'partially_received' => 'info', default => 'warning' }">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($order->canBeReceived())
                                    <flux:button wire:click="openReceiveModal({{ $order->id }})" size="sm" variant="primary">
                                        {{ __('Receive') }}
                                    </flux:button>
                                @endif
                                @if($order->canBeCancelled())
                                    <flux:button wire:click="cancelPurchaseOrder({{ $order->id }})"
                                        wire:confirm="{{ __('Are you sure you want to cancel this purchase order?') }}"
                                        size="sm" variant="ghost">
                                        {{ __('Cancel') }}
                                    </flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="7" :title="__('No purchase orders found')" :description="__('Create a purchase order to restock from a supplier.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

    {{-- Receive Modal --}}
    @if($showReceiveModal)
        @php $order = \App\Models\PurchaseOrder::with(['items.product', 'items.productAttribute', 'supplier', 'warehouse'])->find($receivingOrderId); @endphp
        <flux:modal wire:model="showReceiveModal" name="receive-po-modal" class="max-w-2xl">
            @if($order)
                <form wire:submit.prevent="receivePurchaseOrder" class="space-y-6">
                    <div>
                        <flux:heading>{{ __('Receive Purchase Order') }}</flux:heading>
                        <flux:text size="sm" variant="subtle" class="mt-0.5">
                            {{ $order->order_number }} &middot; {{ $order->supplier->name }} &rarr; {{ $order->warehouse->name }}
                        </flux:text>
                    </div>

                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700 space-y-3">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $item->product->name_en }}</div>
                                        @if($item->productAttribute)
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->productAttribute->attribute_label }}</div>
                                        @endif
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Ordered: :ordered, already received: :received', ['ordered' => $item->quantity_ordered, 'received' => $item->quantity_received]) }}
                                        </div>
                                    </div>
                                    <flux:input type="number" min="0" wire:model="receiveQuantities.{{ $item->id }}" class="w-28" />
                                </div>

                                @if($item->product->tracks_batches && ! $item->product_attribute_id)
                                    <div class="grid grid-cols-2 gap-3">
                                        <flux:field>
                                            <flux:label>{{ __('Batch #') }}</flux:label>
                                            <flux:input wire:model="receiveBatchNumbers.{{ $item->id }}" placeholder="{{ __('e.g. LOT-2026-07') }}" />
                                            <flux:error name="receiveBatchNumbers.{{ $item->id }}" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('Expires') }}</flux:label>
                                            <flux:input type="date" wire:model="receiveExpiryDates.{{ $item->id }}" />
                                        </flux:field>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3">
                        <flux:button type="button" wire:click="closeReceiveModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="receivePurchaseOrder">
                            {{ __('Confirm Receipt') }}
                        </flux:button>
                    </div>
                </form>
            @endif
        </flux:modal>
    @endif
</div>
