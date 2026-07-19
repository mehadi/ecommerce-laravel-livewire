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
                    <flux:heading size="xl">{{ __('Products') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-1">
                        {{ __('See which products are performing and which need restocking.') }}
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
            :metrics="$this->orderedMetricCards->map(fn ($card) => array_merge($card, [
                'value' => $this->getMetricValue($card['key']),
                'subtitle' => $this->getMetricSubtitle($card['key']),
            ]))"
            :charts="$this->orderedChartCards"
            :insights="$this->dashboardInsights"
            :is-customizing="$isCustomizing"
            :chart-data="$this->chartDataBundle()"
        />

        <!-- Low Stock Products Alert -->
        @if($this->lowStockProducts->count() > 0)
            <div class="rounded-2xl border border-amber-200/70 bg-amber-50/60 p-6 shadow-sm dark:border-amber-900/50 dark:bg-amber-900/10">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </span>
                    <h3 class="text-lg font-semibold text-amber-900 dark:text-amber-100">{{ __('Low Stock Alert') }}</h3>
                </div>
                <div class="mt-4 grid gap-2.5 md:grid-cols-2 lg:grid-cols-5">
                    @foreach($this->lowStockProducts as $product)
                        <div class="rounded-xl border border-amber-200/70 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/40 dark:bg-zinc-900">
                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $product->name_en }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Stock') }}: <span class="font-semibold tabular-nums text-amber-600 dark:text-amber-400">{{ $product->getSyncedStock() }}</span></p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-dashboard.chart-bootstrap
            :chart-data="$this->chartDataBundle()"
            :visible-charts="$this->orderedChartCards->pluck('key')->toArray()"
        />
    </div>
</div>
