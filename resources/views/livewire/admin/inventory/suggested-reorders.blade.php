<div class="space-y-6">
    <x-admin.page-header :heading="__('Suggested Reorders')" :description="__('Low-stock and out-of-stock products grouped by supplier, with a starting-point reorder quantity')">
        <flux:button :href="route('admin.inventory.index')" variant="ghost" wire:navigate>
            {{ __('Back to Inventory') }}
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if($groups->isEmpty())
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-8 text-center">
            <p class="text-zinc-500 dark:text-zinc-400">{{ __('Nothing needs reordering right now — every product is above its low-stock threshold.') }}</p>
        </div>
    @endif

    @foreach($groups as $supplierId => $group)
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700">
                <div>
                    <flux:heading size="lg">{{ $group['supplier']?->name ?? __('No Supplier Assigned') }}</flux:heading>
                    @if($group['supplier']?->lead_time_days !== null)
                        <flux:text size="sm" variant="subtle">{{ __('Lead time: :days day(s)', ['days' => $group['supplier']->lead_time_days]) }}</flux:text>
                    @endif
                </div>
                @if($group['supplier'])
                    <flux:button wire:click="createPurchaseOrder({{ $supplierId }})" variant="primary" size="sm">
                        {{ __('Create Purchase Order') }}
                    </flux:button>
                @else
                    <flux:text size="sm" variant="subtle">{{ __('Assign a default supplier to these products to enable one-click ordering.') }}</flux:text>
                @endif
            </div>

            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Product') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Current Stock') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Threshold') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Suggested Reorder Qty') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($group['products'] as $product)
                        @php
                            $stock = $product->getSyncedStock();
                            $suggested = max(1, ($product->lowStockThreshold() * 2) - $stock);
                        @endphp
                        <tr>
                            <td class="px-6 py-3 text-sm text-zinc-900 dark:text-white">{{ $product->name_en }}</td>
                            <td class="px-6 py-3 text-sm">
                                @if($stock <= 0)
                                    <flux:badge variant="danger" size="sm">{{ __('Out of Stock') }}</flux:badge>
                                @else
                                    <flux:badge variant="warning" size="sm">{{ $stock }} {{ __('units') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $product->lowStockThreshold() }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-zinc-900 dark:text-white">{{ $suggested }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</div>
