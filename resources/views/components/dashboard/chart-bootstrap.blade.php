@props([
    // Full chart-key => {labels, data, ...} payload for this page, typically
    // $this->chartDataBundle() filtered down to this page's chart keys (or
    // the full bundle — resources/js/dashboard.js only reads keys that have
    // a matching canvas + chartConfigs entry on the page).
    'chartData' => [],

    // Chart keys that should actually be rendered on this page, typically
    // $this->orderedChartCards->pluck('key') — drives the lazy
    // IntersectionObserver render in resources/js/dashboard.js.
    'visibleCharts' => [],
])

<script>
    window.dashboardChartData = @json($chartData);
    window.dashboardVisibleCharts = @json($visibleCharts);
    document.dispatchEvent(new CustomEvent('dashboard:bootstrap'));
</script>
