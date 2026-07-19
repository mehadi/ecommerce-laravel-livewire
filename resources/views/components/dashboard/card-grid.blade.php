@props([
    // Ordered, visibility-filtered metric cards for this page (from
    // $this->orderedMetricCards), with 'value' and 'subtitle' already merged
    // onto each card array by the caller (see getMetricValue()/
    // getMetricSubtitle() on HasDashboardAnalytics). Pass null (default) if
    // this page has no metric cards at all — the section is skipped
    // entirely instead of showing an empty state.
    'metrics' => null,

    // Ordered, visibility-filtered chart cards for this page (from
    // $this->orderedChartCards). Pass null (default) if this page has no
    // chart cards at all — the section is skipped entirely.
    'charts' => null,

    // Insight callouts for this page (from $this->dashboardInsights, already
    // filtered to the insight keys assigned to this page since
    // availableInsightCards() is page-scoped). Defaults to an empty array —
    // renders nothing when empty, no empty-state (matches original).
    'insights' => [],

    // Whether the dashboard is currently in drag/hide customization mode.
    'isCustomizing' => false,

    // Full chart-key => {labels, data, ...} payload for this page, typically
    // $this->chartDataBundle(). Used only to decide whether a given chart
    // card's underlying dataset is empty, so chart-card can render the "No
    // data for this period" placeholder instead of a blank canvas. Optional —
    // defaults to [] so pages with no chart cards need not pass it.
    'chartData' => [],
])

@if(count($insights) > 0)
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($insights as $index => $insight)
            <flux:callout
                icon="{{ $insight['icon'] }}"
                variant="{{ $insight['variant'] }}"
                wire:key="insight-{{ $index }}"
            >
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $insight['title'] }}</p>
                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">{{ $insight['body'] }}</p>
            </flux:callout>
        @endforeach
    </div>
@endif

@if($metrics !== null)
    @if(count($metrics) > 0)
        <div
            id="metrics-container"
            class="grid gap-4 md:grid-cols-2 lg:grid-cols-4"
        >
            @foreach($metrics as $card)
                <x-dashboard.metric-card
                    :card-key="$card['key']"
                    :card="$card"
                    :is-customizing="$isCustomizing"
                    :value="$card['value'] ?? null"
                    :subtitle="$card['subtitle'] ?? null"
                />
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/60 p-12 text-center dark:border-zinc-700 dark:bg-white/[2%]">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-zinc-400 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">{{ __('No metric cards visible') }}</p>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enable metric cards in customization mode to view your key metrics.') }}</p>
            <flux:button wire:click="toggleCustomization" variant="primary" size="sm" class="mt-4">
                {{ __('Customize Dashboard') }}
            </flux:button>
        </div>
    @endif
@endif

@if($charts !== null)
    @if(count($charts) > 0)
        <div
            id="charts-container"
            class="grid gap-6 lg:grid-cols-2"
        >
            @foreach($charts as $card)
                <x-dashboard.chart-card
                    :card-key="$card['key']"
                    :card="$card"
                    :is-customizing="$isCustomizing"
                    :empty="empty(data_get($chartData, $card['key'].'.labels', []))"
                />
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/60 p-12 text-center dark:border-zinc-700 dark:bg-white/[2%]">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-zinc-400 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">{{ __('No chart cards visible') }}</p>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enable chart cards in customization mode to view your analytics.') }}</p>
            <flux:button wire:click="toggleCustomization" variant="primary" size="sm" class="mt-4">
                {{ __('Customize Dashboard') }}
            </flux:button>
        </div>
    @endif
@endif
