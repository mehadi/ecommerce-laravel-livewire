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
                    <flux:heading size="xl">{{ __('Customers') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-1">
                        {{ __('Understand customer loyalty, repeat purchases, and regional trends.') }}
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

        <x-dashboard.chart-bootstrap
            :chart-data="$this->chartDataBundle()"
            :visible-charts="$this->orderedChartCards->pluck('key')->toArray()"
        />
    </div>
</div>
