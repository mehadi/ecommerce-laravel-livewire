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
                    <flux:heading size="xl">{{ __('Sales & Revenue') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-1">
                        {{ __('Track revenue, discounts, and payment trends over time.') }}
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

        <x-dashboard.card-grid
            :metrics="$this->orderedMetricCards->map(fn ($card) => [
                ...$card,
                'value' => $this->getMetricValue($card['key']),
                'subtitle' => $this->getMetricSubtitle($card['key']),
            ])"
            :charts="$this->orderedChartCards"
            :insights="$this->dashboardInsights"
            :is-customizing="$isCustomizing"
            :chart-data="$this->chartDataBundle()"
        />

        <!-- Coupon Performance -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-lg ring-1 ring-zinc-100 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800/50">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Coupon Performance') }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Which discount codes are actually driving usage.') }}</p>
                </div>
            </div>

            @if(! $hasAdvancedAnalytics)
                <div class="mt-4">
                    <x-dashboard.locked-report
                        :title="__('Coupon Performance')"
                        :description="__('See per-coupon redemption performance on a plan with Advanced Analytics.')"
                    />
                </div>
            @elseif($this->couponPerformanceData->isEmpty())
                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No coupons have been created yet.') }}</p>
            @else
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Code') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Discount') }}</th>
                                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Redemptions') }}</th>
                                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Usage') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($this->couponPerformanceData as $row)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white">{{ $row['code'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $row['type'] === 'percentage' ? number_format($row['value'], 0).'%' : \App\Models\Setting::get('currency_symbol', '৳').number_format($row['value'], 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-900 dark:text-white">{{ number_format($row['used_count']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-zinc-600 dark:text-zinc-400">
                                        {{ $row['usage_percent'] !== null ? number_format($row['usage_percent'], 1).'%' : __('Unlimited') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $row['is_valid'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                            {{ $row['is_valid'] ? __('Active') : __('Inactive/Expired') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Redemption counts are aggregate totals — individual orders are not attributed to a specific coupon in this system.') }}
                </p>
            @endif
        </div>

        <x-dashboard.chart-bootstrap
            :chart-data="$this->chartDataBundle()"
            :visible-charts="$this->orderedChartCards->pluck('key')->toArray()"
        />
    </div>
</div>
