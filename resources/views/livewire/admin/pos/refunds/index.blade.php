<div class="space-y-6">
    <x-admin.page-header :heading="__('POS Refunds')" :description="__('Process full or partial refunds on in-person sales')" />

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif
    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by order number or customer...') }}" />
            </flux:field>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Order') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Customer') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Refunds') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($orders as $order)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('M j, Y g:i A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($order->refunds_count > 0)
                                <flux:badge variant="subtle" size="sm">{{ $order->refunds_count }}</flux:badge>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:button wire:click="openRefundModal({{ $order->id }})" size="sm" variant="ghost">{{ __('Refund') }}</flux:button>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('No POS orders found')" :description="__('POS sales will appear here once the terminal has processed a sale.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l-4 4m0 0l4 4m-4-4h11a4 4 0 000-8h-1"></path></svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif

    @if($showRefundModal && $refundingOrder)
        <flux:modal wire:model="showRefundModal" name="refund-modal">
            <form wire:submit.prevent="submitRefund" class="space-y-6">
                <flux:heading>{{ __('Refund Order :number', ['number' => $refundingOrder->order_number]) }}</flux:heading>

                <div class="space-y-2">
                    @foreach ($refundingOrder->items as $item)
                        @php($refundable = $item->quantity - $item->refundedQuantity())
                        <div class="flex items-center justify-between gap-3 rounded-md border border-zinc-200 p-2 dark:border-zinc-700">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium">{{ $item->product_name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Sold') }}: {{ $item->quantity }} &middot; {{ __('Refundable') }}: {{ $refundable }}</div>
                            </div>
                            <flux:input type="number" min="0" max="{{ $refundable }}" wire:model="refundQuantities.{{ $item->id }}" class="w-20" :disabled="$refundable <= 0" />
                        </div>
                    @endforeach
                </div>

                <flux:field>
                    <flux:label>{{ __('Refund Method') }}</flux:label>
                    <flux:select wire:model="refundMethod">
                        <option value="cash">{{ __('Cash') }}</option>
                        <option value="store_credit">{{ __('Store Credit') }}</option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Reason') }}</flux:label>
                    <flux:textarea wire:model="refundReason" rows="2" />
                </flux:field>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary">{{ __('Process Refund') }}</flux:button>
                    <flux:button type="button" wire:click="closeRefundModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
