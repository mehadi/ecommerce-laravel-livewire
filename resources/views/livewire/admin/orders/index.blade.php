    <div class="space-y-6">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <flux:heading>{{ __('Orders') }}</flux:heading>
                <flux:text size="sm" variant="subtle" class="mt-1">
                    {{ __('Manage and track all customer orders') }}
                </flux:text>
            </div>
            <flux:button wire:click="openCreateOrderModal" variant="primary">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('Create Order') }}</span>
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
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Orders') }}</p>
                        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Revenue') }}</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Pending Orders') }}</p>
                        <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Today\'s Orders') }}</p>
                        <p class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['today'] }}</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">৳{{ number_format($stats['today_revenue'], 2) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters and Search --}}
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <flux:field>
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search orders...') }}" />
                </flux:field>
            </div>
            <flux:field>
                <flux:select wire:model.live="filterStatus">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="confirmed">{{ __('Confirmed') }}</option>
                    <option value="processing">{{ __('Processing') }}</option>
                    <option value="shipped">{{ __('Shipped') }}</option>
                    <option value="delivered">{{ __('Delivered') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:select wire:model.live="filterPaymentStatus">
                    <option value="">{{ __('All Payments') }}</option>
                    <option value="fully_paid">{{ __('Fully Paid') }}</option>
                    <option value="partially_paid">{{ __('Partially Paid') }}</option>
                    <option value="unpaid">{{ __('Unpaid') }}</option>
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:input type="date" wire:model.live="filterDateFrom" placeholder="{{ __('From Date') }}" />
            </flux:field>
            <flux:field>
                <flux:input type="date" wire:model.live="filterDateTo" placeholder="{{ __('To Date') }}" />
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
                    <flux:dropdown>
                        <flux:button size="sm" variant="ghost">
                            {{ __('Update Status') }}
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item wire:click="bulkUpdateStatus('confirmed')">{{ __('Confirm') }}</flux:menu.item>
                            <flux:menu.item wire:click="bulkUpdateStatus('processing')">{{ __('Processing') }}</flux:menu.item>
                            <flux:menu.item wire:click="bulkUpdateStatus('shipped')">{{ __('Shipped') }}</flux:menu.item>
                            <flux:menu.item wire:click="bulkUpdateStatus('delivered')">{{ __('Delivered') }}</flux:menu.item>
                            <flux:menu.item wire:click="bulkUpdateStatus('cancelled')" class="text-red-600 dark:text-red-400">{{ __('Cancel') }}</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                        </th>
                        <th wire:click="sortBy('order_number')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700">
                            <div class="flex items-center gap-2">
                                {{ __('Order #') }}
                                @if($sortField === 'order_number')
                                    @if($sortDirection === 'asc')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th wire:click="sortBy('total')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700">
                            <div class="flex items-center gap-2">
                                {{ __('Total') }}
                                @if($sortField === 'total')
                                    @if($sortDirection === 'asc')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Shipping') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Payment') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700">
                            <div class="flex items-center gap-2">
                                {{ __('Date') }}
                                @if($sortField === 'created_at')
                                    @if($sortDirection === 'asc')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:checkbox wire:model.live="selectedItems" value="{{ $order->id }}" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-mono text-gray-900 dark:text-white font-medium">{{ $order->order_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->customer_email }}</div>
                                    @if($order->customer_phone)
                                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $order->customer_phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->total, 2) }}</div>
                                @if($order->discount > 0)
                                    <div class="text-xs text-red-600 dark:text-red-400">-৳{{ number_format($order->discount, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->shipping_cost > 0)
                                    <div class="font-medium text-blue-600 dark:text-blue-400">৳{{ number_format($order->shipping_cost, 2) }}</div>
                                    @if($order->shipping_city)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->shipping_city }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    @if($order->advance_payment > 0)
                                        <div class="text-sm font-medium text-green-600 dark:text-green-400">৳{{ number_format($order->advance_payment, 2) }}</div>
                                        @if($order->isFullyPaid())
                                            <flux:badge size="sm" variant="success">{{ __('Paid') }}</flux:badge>
                                        @else
                                            <div class="text-xs text-orange-600 dark:text-orange-400">
                                                {{ __('Remaining: ৳:amount', ['amount' => number_format($order->remaining_amount, 2)]) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @php
                                        $statusVariants = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'processing' => 'primary',
                                            'shipped' => 'info',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                        ];
                                        $statusVariant = $statusVariants[$order->status] ?? 'subtle';
                                    @endphp
                                    <flux:badge :variant="$statusVariant">
                                        {{ ucfirst(__($order->status)) }}
                                    </flux:badge>
                                    <flux:dropdown>
                                        <flux:button size="sm" variant="ghost" class="p-1">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </flux:button>
                                        <flux:menu>
                                            <flux:menu.item wire:click="updateOrderStatus({{ $order->id }}, 'pending')" :class="$order->status === 'pending' ? 'bg-blue-50 dark:bg-blue-900/20' : ''">{{ __('Pending') }}</flux:menu.item>
                                            <flux:menu.item wire:click="updateOrderStatus({{ $order->id }}, 'confirmed')" :class="$order->status === 'confirmed' ? 'bg-blue-50 dark:bg-blue-900/20' : ''">{{ __('Confirmed') }}</flux:menu.item>
                                            <flux:menu.item wire:click="updateOrderStatus({{ $order->id }}, 'processing')" :class="$order->status === 'processing' ? 'bg-blue-50 dark:bg-blue-900/20' : ''">{{ __('Processing') }}</flux:menu.item>
                                            <flux:menu.item wire:click="updateOrderStatus({{ $order->id }}, 'shipped')" :class="$order->status === 'shipped' ? 'bg-blue-50 dark:bg-blue-900/20' : ''">{{ __('Shipped') }}</flux:menu.item>
                                            <flux:menu.item wire:click="updateOrderStatus({{ $order->id }}, 'delivered')" :class="$order->status === 'delivered' ? 'bg-blue-50 dark:bg-blue-900/20' : ''">{{ __('Delivered') }}</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="updateOrderStatus({{ $order->id }}, 'cancelled')" class="text-red-600 dark:text-red-400">{{ __('Cancel') }}</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <flux:button wire:click="viewOrder({{ $order->id }})" size="sm" variant="ghost">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>{{ __('View') }}</span>
                                        </span>
                                    </flux:button>
                                    @if($order->advance_payment > 0)
                                        <flux:button wire:click="openEditAdvancePayment({{ $order->id }})" size="sm" variant="ghost">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ __('Payment') }}</span>
                                            </span>
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('No orders found') }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Get started by creating your first order.') }}
                                        </p>
                                    </div>
                                    <flux:button wire:click="openCreateOrderModal" variant="primary" size="sm">
                                        {{ __('Create Order') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($showOrderDetailsModal && $selectedOrder)
            <flux:modal wire:model="showOrderDetailsModal" name="order-details">
                <div class="space-y-6">
                    <flux:heading>{{ __('Order Details') }} #{{ $selectedOrder->order_number }}</flux:heading>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold mb-2">{{ __('Customer Information') }}</h3>
                            <p class="text-gray-900 dark:text-white">{{ $selectedOrder->customer_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedOrder->customer_email }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedOrder->customer_phone }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">{{ __('Shipping Address') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $selectedOrder->shipping_address }}</p>
                            @if($selectedOrder->shipping_city)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedOrder->shipping_city }}</p>
                            @endif
                            @if($selectedOrder->shipping_postal_code)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedOrder->shipping_postal_code }}</p>
                            @endif
                        </div>
                    </div>

                    @if($selectedOrder->shipping_cost > 0)
                        <div class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                            <h3 class="font-semibold mb-3 flex items-center gap-2 text-blue-900 dark:text-blue-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                {{ __('Shipping Information') }}
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Shipping Cost') }}:</span>
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">৳{{ number_format($selectedOrder->shipping_cost, 2) }}</span>
                                </div>
                                @if($selectedOrder->shipping_city)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Shipping City') }}:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->shipping_city }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Shipping Address') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-white text-right max-w-xs">{{ $selectedOrder->shipping_address }}</span>
                                </div>
                                @if($selectedOrder->shipping_postal_code)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Postal Code') }}:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->shipping_postal_code }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-semibold mb-2">{{ __('Order Items') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Product') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Quantity') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Price') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($selectedOrder->orderItems as $item)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <div class="text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                                @if($item->attribute_data)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        @foreach($item->attribute_data as $key => $value)
                                                            <span class="inline-block mr-2">{{ $key }}: {{ $value }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">৳{{ number_format($item->price, 2) }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">৳{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Subtotal') }}:</span>
                            <span class="text-gray-900 dark:text-white">৳{{ number_format($selectedOrder->subtotal, 2) }}</span>
                        </div>
                        @if($selectedOrder->discount > 0)
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('Discount') }}:</span>
                                <span class="text-red-600 dark:text-red-400">-৳{{ number_format($selectedOrder->discount, 2) }}</span>
                            </div>
                        @endif
                        @if($selectedOrder->shipping_cost > 0)
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('Shipping') }}:</span>
                                <span class="text-blue-600 dark:text-blue-400 font-medium">৳{{ number_format($selectedOrder->shipping_cost, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg">
                            <span>{{ __('Total') }}:</span>
                            <span class="text-gray-900 dark:text-white">৳{{ number_format($selectedOrder->total, 2) }}</span>
                        </div>
                        @if($selectedOrder->advance_payment > 0)
                            <div class="flex justify-between mt-2 pt-2 border-t">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('Advance Payment') }}:</span>
                                <span class="text-green-600 dark:text-green-400 font-semibold">৳{{ number_format($selectedOrder->advance_payment, 2) }}</span>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Remaining Amount') }}:</span>
                                <span class="text-orange-600 dark:text-orange-400 font-bold">৳{{ number_format($selectedOrder->remaining_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-semibold">{{ __('Payment Information') }}</span>
                            <flux:button wire:click="openEditAdvancePayment({{ $selectedOrder->id }})" size="sm" variant="ghost">
                                {{ __('Edit Advance Payment') }}
                            </flux:button>
                        </div>
                        <div class="space-y-2 text-sm">
                            <p class="text-gray-600 dark:text-gray-400">{{ __('Payment Method') }}: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $selectedOrder->payment_method)) }}</span></p>
                            @if($selectedOrder->advance_payment > 0)
                                @if($selectedOrder->advance_payment_method)
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('Advance Payment Method') }}: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $selectedOrder->advance_payment_method)) }}</span></p>
                                @endif
                                @if($selectedOrder->transaction_id)
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('Transaction ID') }}: <span class="font-medium text-gray-900 dark:text-white font-mono">{{ $selectedOrder->transaction_id }}</span></p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <flux:button wire:click="$set('showOrderDetailsModal', false)" variant="ghost">{{ __('Close') }}</flux:button>
                    </div>
                </div>
            </flux:modal>
        @endif

        @if($showCreateOrderModal)
            <flux:modal wire:model="showCreateOrderModal" name="create-order-modal">
                <form wire:submit.prevent="createOrder" class="space-y-6">
                    <flux:heading>{{ __('Create New Order') }}</flux:heading>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Customer Name') }} *</flux:label>
                            <flux:input wire:model="customer_name" />
                            <flux:error name="customer_name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Customer Email') }} *</flux:label>
                            <flux:input type="email" wire:model="customer_email" />
                            <flux:error name="customer_email" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Customer Phone') }} *</flux:label>
                            <flux:input wire:model="customer_phone" />
                            <flux:error name="customer_phone" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Payment Method') }} *</flux:label>
                            <flux:select wire:model="payment_method">
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                <option value="mobile_banking">{{ __('Mobile Banking') }}</option>
                            </flux:select>
                            <flux:error name="payment_method" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Shipping Address') }} *</flux:label>
                        <flux:textarea wire:model="shipping_address" rows="2" />
                        <flux:error name="shipping_address" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('City') }}</flux:label>
                            <flux:input wire:model="shipping_city" />
                            <flux:error name="shipping_city" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Postal Code') }}</flux:label>
                            <flux:input wire:model="shipping_postal_code" />
                            <flux:error name="shipping_postal_code" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Status') }} *</flux:label>
                            <flux:select wire:model="status">
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="confirmed">{{ __('Confirmed') }}</option>
                                <option value="processing">{{ __('Processing') }}</option>
                                <option value="shipped">{{ __('Shipped') }}</option>
                                <option value="delivered">{{ __('Delivered') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Notes') }}</flux:label>
                            <flux:textarea wire:model="notes" rows="2" />
                            <flux:error name="notes" />
                        </flux:field>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-4">{{ __('Advance Payment Information (Optional)') }}</h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <flux:field>
                                <flux:label>{{ __('Advance Payment Amount') }}</flux:label>
                                <flux:input type="number" wire:model="advance_payment" step="0.01" min="0" :max="$this->total" placeholder="0.00" />
                                <flux:error name="advance_payment" />
                                <flux:description>{{ __('Remaining: ৳') }}{{ number_format(max(0, $this->total - ($advance_payment ?? 0)), 2) }}</flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Payment Method') }}</flux:label>
                                <flux:select wire:model="advance_payment_method">
                                    <option value="">{{ __('Select Payment Method') }}</option>
                                    <option value="cash">{{ __('Cash') }}</option>
                                    <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                    <option value="mobile_banking">{{ __('Mobile Banking') }}</option>
                                    <option value="bkash">{{ __('bKash') }}</option>
                                    <option value="nagad">{{ __('Nagad') }}</option>
                                    <option value="rocket">{{ __('Rocket') }}</option>
                                </flux:select>
                                <flux:error name="advance_payment_method" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>{{ __('Transaction ID') }}</flux:label>
                            <flux:input wire:model="transaction_id" placeholder="{{ __('Enter transaction ID if applicable') }}" />
                            <flux:error name="transaction_id" />
                            <flux:description>{{ __('Transaction ID from payment gateway or bank') }}</flux:description>
                        </flux:field>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-4">{{ __('Order Items') }}</h3>
                        
                        <div class="flex gap-2 mb-4">
                            <flux:field class="flex-1">
                                <flux:select wire:model="selectedProductId">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach($products as $product)
                                        @php
                                            $syncedPrice = $product->getSyncedPrice();
                                        @endphp
                                        <option value="{{ $product->id }}">{{ $product->name_en }} - ৳{{ number_format($syncedPrice, 2) }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="selectedProductQuantity" min="1" placeholder="{{ __('Qty') }}" />
                            </flux:field>
                            <flux:button type="button" wire:click="addOrderItem" variant="primary">
                                {{ __('Add') }}
                            </flux:button>
                        </div>

                        @if(count($orderItems) > 0)
                            <div class="overflow-x-auto mb-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                    <thead class="bg-gray-50 dark:bg-zinc-800">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Product') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Price') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Quantity') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Subtotal') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                                        @foreach($orderItems as $index => $item)
                                            <tr>
                                                <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $item['product_name'] }}</td>
                                                <td class="px-4 py-2 text-gray-900 dark:text-white">৳{{ number_format($item['price'], 2) }}</td>
                                                <td class="px-4 py-2">
                                                    <flux:input type="number" wire:model.live.debounce.300ms="orderItems.{{ $index }}.quantity" min="1" class="w-20" />
                                                </td>
                                                <td class="px-4 py-2 text-gray-900 dark:text-white">৳{{ number_format($item['subtotal'], 2) }}</td>
                                                <td class="px-4 py-2">
                                                    <flux:button type="button" wire:click="removeOrderItem({{ $index }})" size="sm" variant="danger">
                                                        {{ __('Remove') }}
                                                    </flux:button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 dark:bg-zinc-800">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">{{ __('Total') }}:</td>
                                            <td class="px-4 py-2 font-semibold text-gray-900 dark:text-white">৳{{ number_format($this->total, 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Create Order') }}</flux:button>
                        <flux:button type="button" wire:click="closeCreateOrderModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        @endif

        @if($editingAdvancePayment)
            <flux:modal wire:model="editingAdvancePayment" name="edit-advance-payment-modal">
                <form wire:submit.prevent="updateAdvancePayment" class="space-y-6">
                    <flux:heading>{{ __('Edit Advance Payment') }}</flux:heading>

                    @php
                        $order = $editAdvancePaymentOrderId ? \App\Models\Order::find($editAdvancePaymentOrderId) : null;
                    @endphp

                    @if($order)
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Order Total') }}:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->total, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Current Advance Payment') }}:</span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">৳{{ number_format($order->advance_payment, 2) }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <flux:field>
                                    <flux:label>{{ __('Advance Payment Amount') }} *</flux:label>
                                    <flux:input type="number" wire:model="editAdvancePaymentAmount" step="0.01" min="0" max="{{ $order->total }}" />
                                    <flux:error name="editAdvancePaymentAmount" />
                                    <flux:description>{{ __('Remaining: ৳') }}{{ number_format(max(0, $order->total - ($editAdvancePaymentAmount ?? 0)), 2) }}</flux:description>
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('Payment Method') }}</flux:label>
                                    <flux:select wire:model="editAdvancePaymentMethod">
                                        <option value="">{{ __('Select Payment Method') }}</option>
                                        <option value="cash">{{ __('Cash') }}</option>
                                        <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                        <option value="mobile_banking">{{ __('Mobile Banking') }}</option>
                                        <option value="bkash">{{ __('bKash') }}</option>
                                        <option value="nagad">{{ __('Nagad') }}</option>
                                        <option value="rocket">{{ __('Rocket') }}</option>
                                    </flux:select>
                                    <flux:error name="editAdvancePaymentMethod" />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>{{ __('Transaction ID') }}</flux:label>
                                <flux:input wire:model="editTransactionId" placeholder="{{ __('Enter transaction ID if applicable') }}" />
                                <flux:error name="editTransactionId" />
                                <flux:description>{{ __('Transaction ID from payment gateway or bank') }}</flux:description>
                            </flux:field>
                        </div>
                    @endif

                    <div class="flex gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Update Advance Payment') }}</flux:button>
                        <flux:button type="button" wire:click="closeEditAdvancePayment" variant="ghost">{{ __('Cancel') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        @endif

        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
