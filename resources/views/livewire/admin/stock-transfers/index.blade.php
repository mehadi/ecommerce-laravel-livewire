<div class="space-y-6">
    <x-admin.page-header :heading="__('Stock Transfers')" :description="__('Move stock between warehouses')">
        <flux:button :href="route('admin.stock-transfers.create')" variant="primary" wire:navigate>
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('New Transfer') }}</span>
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
        <x-admin.stat-card :label="__('Total Transfers')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Pending')" :value="$stats['pending']" tone="amber">
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
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by warehouse name...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="in_transit">{{ __('In Transit') }}</option>
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
                    <x-admin.sortable-th field="id" :label="__('ID')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('From') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('To') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Items') }}</th>
                    <x-admin.sortable-th field="status" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="created_at" :label="__('Created')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($transfers as $transfer)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-500 dark:text-zinc-400">#{{ $transfer->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->fromWarehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->toWarehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ __(':count item(s)', ['count' => $transfer->items->count()]) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge size="sm" :variant="match($transfer->status) { 'received' => 'success', 'cancelled' => 'danger', 'in_transit' => 'info', default => 'warning' }">
                                {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $transfer->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($transfer->canBeReceived())
                                    <flux:button wire:click="openReceiveModal({{ $transfer->id }})" size="sm" variant="primary">
                                        {{ __('Receive') }}
                                    </flux:button>
                                @endif
                                @if($transfer->canBeCancelled())
                                    <flux:button wire:click="cancelTransfer({{ $transfer->id }})"
                                        wire:confirm="{{ __('Are you sure you want to cancel this transfer?') }}"
                                        size="sm" variant="ghost">
                                        {{ __('Cancel') }}
                                    </flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="7" :title="__('No stock transfers found')" :description="__('Create a transfer to move stock between warehouses.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transfers->hasPages())
        <div class="mt-4">
            {{ $transfers->links() }}
        </div>
    @endif

    {{-- Receive Modal --}}
    @if($showReceiveModal)
        @php $transfer = \App\Models\StockTransfer::with(['items.product', 'items.productAttribute', 'fromWarehouse', 'toWarehouse'])->find($receivingTransferId); @endphp
        <flux:modal wire:model="showReceiveModal" name="receive-transfer-modal" class="max-w-2xl">
            @if($transfer)
                <form wire:submit.prevent="receiveTransfer" class="space-y-6">
                    <div>
                        <flux:heading>{{ __('Receive Stock Transfer') }}</flux:heading>
                        <flux:text size="sm" variant="subtle" class="mt-0.5">
                            {{ $transfer->fromWarehouse->name }} &rarr; {{ $transfer->toWarehouse->name }}
                        </flux:text>
                    </div>

                    <div class="space-y-3">
                        @foreach($transfer->items as $item)
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $item->product->name_en }}</div>
                                    @if($item->productAttribute)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->productAttribute->attribute_label }}</div>
                                    @endif
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Transferred quantity: :qty', ['qty' => $item->quantity]) }}</div>
                                </div>
                                <flux:input type="number" min="0" wire:model="receiveQuantities.{{ $item->id }}" class="w-28" />
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3">
                        <flux:button type="button" wire:click="closeReceiveModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="receiveTransfer">
                            {{ __('Confirm Receipt') }}
                        </flux:button>
                    </div>
                </form>
            @endif
        </flux:modal>
    @endif
</div>
