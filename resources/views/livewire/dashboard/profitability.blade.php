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
                <flux:heading size="xl">{{ __('Product Profitability') }}</flux:heading>
                <flux:text size="sm" variant="subtle" class="mt-1">
                    {{ __('Which products actually make money, not just which sell the most.') }}
                </flux:text>
            </div>

            @include('components.dashboard.filters')
        </div>

        <x-dashboard.sub-nav />

        @if(! $hasAdvancedAnalytics)
            <x-dashboard.locked-report
                :title="__('Product Profitability & Margin')"
                :description="__('See per-product revenue, cost, and margin on a plan with Advanced Analytics.')"
            />
        @else
            @php($summary = $this->profitabilitySummary)

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Estimated Gross Profit') }}</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($summary['total_profit'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Revenue (period)') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($summary['total_revenue'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Gross Margin %') }}</p>
                    <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($summary['margin_percent'], 1) }}%</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Products Missing Cost Price') }}</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $summary['missing_cost_count'] }}</p>
                </div>
            </div>

            @if($summary['missing_cost_count'] > 0)
                <flux:callout variant="warning">
                    {{ __(':count product(s) sold in this period have no cost price (buying price) set — their profit is shown as 0. Set a buying price on the product to include it in margin calculations.', ['count' => $summary['missing_cost_count']]) }}
                </flux:callout>
            @endif

            <div class="rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="flex flex-col gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:flex-row md:items-center md:justify-between">
                    <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search products...')" class="md:max-w-xs" />
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <x-admin.sortable-th field="product_name" label="{{ __('Product') }}" :sort-field="$sortField" :sort-direction="$sortDirection" />
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Category') }}</th>
                                <x-admin.sortable-th field="units_sold" label="{{ __('Units Sold') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="revenue" label="{{ __('Revenue') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="cost" label="{{ __('Cost') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="profit" label="{{ __('Profit') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
                                <x-admin.sortable-th field="margin_percent" label="{{ __('Margin %') }}" :sort-field="$sortField" :sort-direction="$sortDirection" align="right" />
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
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ number_format($row['units_sold']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($row['revenue'], 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-600 dark:text-zinc-400">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($row['cost'], 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold tabular-nums {{ $row['profit'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($row['profit'], 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ number_format($row['margin_percent'], 1) }}%</td>
                                </tr>
                            @empty
                                <x-admin.table-empty-state colspan="7" title="{{ __('No product sales in this period.') }}" />
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
