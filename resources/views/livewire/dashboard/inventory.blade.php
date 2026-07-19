<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @if (session()->has('message'))
            <flux:callout variant="success">{{ session('message') }}</flux:callout>
        @endif

        @if (session()->has('error'))
            <flux:callout variant="danger">{{ session('error') }}</flux:callout>
        @endif

        <!-- Header with Filters -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="xl">{{ __('Inventory Valuation') }}</flux:heading>
                <flux:text size="sm" variant="subtle" class="mt-1">
                    {{ __('Capital tied up in stock, and what to reorder vs. what to write off.') }}
                </flux:text>
            </div>

            @include('components.dashboard.filters')
        </div>

        <x-dashboard.sub-nav />

        @if(! $hasAdvancedAnalytics)
            <x-dashboard.locked-report
                :title="__('Inventory Valuation & Reorder')"
                :description="__('See stock value, low-stock, and dead-stock analysis on a plan with Advanced Analytics.')"
            />
        @else
            @php($summary = $this->inventorySummary)

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Inventory Value (at cost)') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($summary['total_value_at_cost'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Inventory Value (at retail)') }}</p>
                    <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($summary['total_value_at_retail'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Low Stock SKUs') }}</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $summary['low_stock_count'] }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Dead Stock SKUs (no sales in period)') }}</p>
                    <p class="mt-1 text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $summary['dead_stock_count'] }}</p>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="flex flex-col gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:flex-row md:items-center md:flex-wrap">
                    <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search products...')" class="md:max-w-xs" />

                    <flux:select wire:model.live="categoryFilter" class="md:max-w-xs">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="stockFilter" class="md:max-w-xs">
                        <option value="">{{ __('All Stock Levels') }}</option>
                        <option value="low_stock">{{ __('Low Stock Only') }}</option>
                        <option value="dead_stock">{{ __('Dead Stock Only') }}</option>
                    </flux:select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <x-admin.sortable-th field="product_name" label="{{ __('Product') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Category') }}</th>
                                <x-admin.sortable-th field="stock" label="{{ __('Stock') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="value_at_cost" label="{{ __('Value (cost)') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="value_at_retail" label="{{ __('Value (retail)') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="units_sold_in_period" label="{{ __('Units Sold (period)') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Flags') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                            @forelse($this->rows as $row)
                                <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <flux:link :href="route('admin.products.edit', $row['product_id'])" wire:navigate class="text-sm font-medium">
                                            {{ $row['product_name'] }}
                                        </flux:link>
                                        @if(! $row['has_cost_data'])
                                            <span class="ml-1 text-xs text-amber-600 dark:text-amber-400" title="{{ __('No cost price set') }}">*</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $row['category'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ number_format($row['stock']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($row['value_at_cost'], 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-600 dark:text-zinc-400">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($row['value_at_retail'], 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ number_format($row['units_sold_in_period']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if($row['is_out_of_stock'])
                                                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ __('Out of Stock') }}</span>
                                            @elseif($row['is_low_stock'])
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">{{ __('Low Stock') }}</span>
                                            @endif
                                            @if($row['is_dead_stock'])
                                                <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">{{ __('Dead Stock') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <x-admin.table-empty-state colspan="7" title="{{ __('No products match these filters.') }}" />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $this->rows->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
