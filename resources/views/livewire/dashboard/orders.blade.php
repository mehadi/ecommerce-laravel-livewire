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
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <flux:heading size="xl">{{ __('Orders & Fulfillment') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-1">
                        {{ __('Monitor order volume, status breakdown, and fulfillment health.') }}
                    </flux:text>
                </div>
                @include('components.dashboard.toolbar-actions')
            </div>

            @include('components.dashboard.filters')
        </div>

        <x-dashboard.sub-nav />

        <!-- Customization Panel -->
        @if($isCustomizing)
            <x-dashboard.customize-panel>
                <div class="space-y-8">
                    <x-dashboard.customize-section
                        icon-path='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />'
                        color="cyan"
                        :title="__('Insight Cards')"
                        :items="$this->availableInsightCards"
                        type="insight"
                        :user-preferences="$this->userPreferences"
                    />

                    <x-dashboard.customize-section
                        icon-path='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'
                        color="emerald"
                        :title="__('Metric Cards')"
                        :items="$this->availableMetricCards"
                        type="metric"
                        :user-preferences="$this->userPreferences"
                    />

                    <x-dashboard.customize-section
                        icon-path='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'
                        color="indigo"
                        :title="__('Chart Cards')"
                        :items="$this->availableChartCards"
                        type="chart"
                        :user-preferences="$this->userPreferences"
                    />
                </div>
            </x-dashboard.customize-panel>
        @endif

        @php
            $statusSummary = $this->statusSummary;
            $recentOrders = $this->recentOrders;
        @endphp

        <x-dashboard.card-grid
            :metrics="$this->orderedMetricCards->map(function (array $card) {
                $card['value'] = $this->getMetricValue($card['key']);
                $card['subtitle'] = $this->getMetricSubtitle($card['key']);

                return $card;
            })"
            :charts="$this->orderedChartCards"
            :insights="$this->dashboardInsights"
            :is-customizing="$isCustomizing"
            :chart-data="$this->chartDataBundle()"
        />

        <!-- Order Status Health & Recent Orders -->
        @if(count($statusSummary) > 0 || $recentOrders->isNotEmpty())
            <div class="grid gap-4 xl:grid-cols-3">
                @if(count($statusSummary) > 0)
                    <div class="xl:col-span-2 rounded-2xl border border-blue-200/50 bg-white p-6 shadow-lg ring-1 ring-blue-100 dark:border-blue-800/40 dark:bg-zinc-900 dark:ring-blue-900/20">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Order Status Health') }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Track fulfillment performance and spot bottlenecks before they slow growth.') }}</p>
                            </div>
                            <div class="flex items-center gap-2 rounded-full border border-blue-200/70 bg-blue-50/60 px-3 py-1 text-xs font-semibold text-blue-700 dark:border-blue-800/60 dark:bg-blue-900/30 dark:text-blue-200">
                                <span class="hidden md:inline">{{ __('Total Orders') }}</span>
                                <span>·</span>
                                <span>{{ number_format($this->totalOrders) }}</span>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($statusSummary as $summary)
                                @php
                                    $badgeClasses = $this->statusBadgeClasses($summary['status']);
                                    $progressClasses = $this->statusProgressClasses($summary['status']);
                                @endphp

                                <div class="flex flex-col rounded-xl border border-zinc-100/80 bg-white/80 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800/60 dark:bg-zinc-900/80">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</p>
                                            <p class="mt-1 text-base font-semibold text-zinc-900 dark:text-white">{{ $summary['label'] }}</p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                            {{ number_format($summary['percentage'], 1) }}%
                                        </span>
                                    </div>

                                    <div class="mt-4">
                                        <p class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($summary['count']) }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('orders') }}</p>
                                    </div>

                                    <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                        <div class="h-full {{ $progressClasses }}" style="width: {{ $summary['percentage'] }}%;"></div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                                        <span>{{ __('Revenue') }}</span>
                                        <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($summary['revenue'], 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($recentOrders->isNotEmpty())
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-lg ring-1 ring-zinc-100 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800/50">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Recent Orders') }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Fresh activity tailored to your current filters.') }}</p>
                            </div>
                            <flux:button :href="route('admin.orders.index')" wire:navigate variant="ghost" size="xs">
                                {{ __('View all') }}
                            </flux:button>
                        </div>

                        <div class="mt-5 space-y-3">
                            @foreach($recentOrders as $order)
                                @php
                                    $statusLabel = $order->status ? $this->statusLabel($order->status) : __('No Status');
                                    $statusChipClasses = $this->statusChipClasses($order->status ?? '');
                                    $statusAccent = $this->statusProgressClasses($order->status ?? '');
                                    $itemsCount = $order->items?->sum('quantity') ?? 0;
                                @endphp

                                <div class="flex items-start justify-between gap-4 rounded-xl border border-zinc-100 bg-white/70 p-4 transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/60">
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                                #{{ $order->order_number ?? $order->id }} · {{ $order->customer_name ?: __('Guest Customer') }}
                                            </p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ optional($order->created_at)->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 text-xs">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold {{ $statusChipClasses }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $statusAccent }}"></span>
                                                {{ $statusLabel }}
                                            </span>
                                            <span class="text-zinc-500 dark:text-zinc-400">{{ $this->formatPaymentMethod($order->payment_method) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format((float) $order->total, 2) }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Items: :count', ['count' => $itemsCount]) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Fulfillment & SLA -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-lg ring-1 ring-zinc-100 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800/50">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Fulfillment & SLA') }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Which open orders have been sitting too long, and how fast orders are actually getting delivered.') }}</p>
                </div>
            </div>

            @if(! $hasAdvancedAnalytics)
                <div class="mt-4">
                    <x-dashboard.locked-report
                        :title="__('Fulfillment & SLA')"
                        :description="__('See time-in-status breakdowns and fulfillment speed on a plan with Advanced Analytics.')"
                    />
                </div>
            @else
                @php($fulfillment = $this->fulfillmentSummary)

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Avg. Fulfillment Time') }}</p>
                        <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">
                            {{ $fulfillment['avg_fulfillment_hours'] !== null ? number_format($fulfillment['avg_fulfillment_hours'], 1).__('h') : __('Not enough data yet') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Open Orders') }}</p>
                        <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">{{ number_format($fulfillment['open_orders_count']) }}</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('SLA Breaches (>:hours h)', ['hours' => $this->slaBreachHours()]) }}</p>
                        <p class="mt-1 text-xl font-bold text-rose-600 dark:text-rose-400">{{ number_format($fulfillment['sla_breach_count']) }}</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Tracked Deliveries') }}</p>
                        <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">{{ number_format($fulfillment['tracked_delivery_count']) }}</p>
                    </div>
                </div>

                @if($fulfillment['avg_fulfillment_hours'] === null)
                    <flux:callout variant="secondary" class="mt-4">
                        {{ __('Fulfillment timing starts accumulating from the date this report was enabled — it needs at least one order to move through a status change after that to show a real average.') }}
                    </flux:callout>
                @endif

                @php($attentionRows = $this->fulfillmentAttentionData->take(25))

                @if($attentionRows->isNotEmpty())
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Order') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Customer') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Time in Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($attentionRows as $row)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white">
                                            <flux:link :href="route('admin.orders.index')" wire:navigate>#{{ $row['order_number'] }}</flux:link>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $row['customer_name'] ?: __('Guest Customer') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full {{ $this->statusChipClasses($row['status']) }} px-2.5 py-1 text-xs font-semibold">
                                                {{ $this->statusLabel($row['status']) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold tabular-nums {{ $row['is_sla_breach'] ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-900 dark:text-white' }}">
                                            {{ number_format($row['hours_in_status'], 0) }}h
                                            @if($row['is_sla_breach'])
                                                <span class="ml-1 inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">{{ __('SLA') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($this->fulfillmentAttentionData->count() > 25)
                        <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Showing the 25 longest-open orders out of :count.', ['count' => $this->fulfillmentAttentionData->count()]) }}</p>
                    @endif
                @else
                    <p class="mt-5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No open orders in this period — nothing needs attention.') }}</p>
                @endif
            @endif
        </div>

        <x-dashboard.chart-bootstrap
            :chart-data="$this->chartDataBundle()"
            :visible-charts="$this->orderedChartCards->pluck('key')->toArray()"
        />
    </div>
</div>
