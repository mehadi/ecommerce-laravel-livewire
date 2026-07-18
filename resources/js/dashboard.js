import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';

/**
 * Dashboard charts + drag-reorder wiring, extracted from the original
 * monolithic dashboard.blade.php inline <script>. Chart.js and SortableJS
 * are now real ES module imports instead of CDN globals; the chartConfigs
 * map below is otherwise unchanged (still generic per chart key, so any
 * dashboard sub-page can bootstrap any subset of these charts via
 * <x-dashboard.chart-bootstrap>).
 */

if (!window.dashboardExistingCharts) {
    window.dashboardExistingCharts = {};
}

let chartObserver = null;
let metricsSortable = null;
let chartsSortable = null;

const chartConfigs = {
    revenue_chart: {
        type: 'line',
        dataset: (data) => [{
            label: 'Revenue',
            data: data.data,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return '৳' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                },
            },
        },
    },
    orders_chart: {
        type: 'bar',
        dataset: (data) => [{
            label: 'Orders',
            data: data.data,
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                },
            },
        },
    },
    status_chart: {
        type: 'doughnut',
        dataset: (data) => [{
            data: data.data,
            backgroundColor: data.colors,
            borderWidth: 2,
            borderColor: '#fff',
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
        },
    },
    products_chart: {
        type: 'bar',
        dataset: (data) => [{
            label: 'Revenue',
            data: data.revenue,
            backgroundColor: 'rgba(139, 92, 246, 0.5)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 1,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return '৳' + context.parsed.x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                },
            },
        },
    },
    top_customers_chart: {
        type: 'bar',
        dataset: (data) => [{
            label: 'Revenue',
            data: data.revenue,
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return '৳' + context.parsed.x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                },
            },
        },
    },
    new_vs_returning_chart: {
        type: 'doughnut',
        dataset: (data) => [{
            data: data.data,
            backgroundColor: data.colors,
            borderWidth: 2,
            borderColor: '#fff',
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
        },
    },
    day_of_week_chart: {
        type: 'bar',
        dataset: (data) => [
            {
                label: 'Orders',
                data: data.orders,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                yAxisID: 'y',
            },
            {
                label: 'Revenue',
                data: data.revenue,
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                yAxisID: 'y1',
            },
        ],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                    grid: { drawOnChartArea: false },
                },
            },
        },
    },
    payment_method_chart: {
        type: 'pie',
        dataset: (data) => [{
            data: data.revenue,
            backgroundColor: data.colors.slice(0, data.revenue.length),
            borderWidth: 2,
            borderColor: '#fff',
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return `${context.label}: ৳${context.parsed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                        },
                    },
                },
            },
        },
    },
    category_chart: {
        type: 'bar',
        dataset: (data) => [{
            label: 'Revenue',
            data: data.revenue,
            backgroundColor: 'rgba(139, 92, 246, 0.5)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 1,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return '৳' + context.parsed.x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                },
            },
        },
    },
    city_chart: {
        type: 'bar',
        dataset: (data) => [{
            label: 'Revenue',
            data: data.revenue,
            backgroundColor: 'rgba(236, 72, 153, 0.5)',
            borderColor: 'rgb(236, 72, 153)',
            borderWidth: 1,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return '৳' + context.parsed.x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                },
            },
        },
    },
    conversion_funnel_chart: {
        type: 'bar',
        dataset: (data) => [{
            label: 'Orders',
            data: data.data,
            backgroundColor: [
                'rgba(251, 191, 36, 0.5)',
                'rgba(59, 130, 246, 0.5)',
                'rgba(168, 85, 247, 0.5)',
                'rgba(99, 102, 241, 0.5)',
                'rgba(16, 185, 129, 0.5)',
            ],
            borderColor: [
                'rgb(251, 191, 36)',
                'rgb(59, 130, 246)',
                'rgb(168, 85, 247)',
                'rgb(99, 102, 241)',
                'rgb(16, 185, 129)',
            ],
            borderWidth: 1,
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                },
            },
        },
    },
    discount_impact_chart: {
        type: 'bar',
        dataset: (data) => [
            {
                label: 'Order Count',
                data: data.count,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                yAxisID: 'y',
            },
            {
                label: 'Avg. Order Value',
                data: data.average_order_value,
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                yAxisID: 'y1',
            },
        ],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '৳' + value.toLocaleString();
                        },
                    },
                    grid: { drawOnChartArea: false },
                },
            },
        },
    },
};

const cloneDataset = (dataset) => {
    const copy = { ...dataset };
    if (Array.isArray(dataset.data)) {
        copy.data = [...dataset.data];
    }
    if (Array.isArray(dataset.backgroundColor)) {
        copy.backgroundColor = [...dataset.backgroundColor];
    }
    if (Array.isArray(dataset.borderColor)) {
        copy.borderColor = [...dataset.borderColor];
    }
    return copy;
};

const renderChart = (chartKey, force = false) => {
    const config = chartConfigs[chartKey];
    const payload = window.dashboardChartData?.[chartKey];
    const canvas = document.getElementById(`${chartKey}Chart`);

    if (!config || !payload || !canvas) {
        return;
    }

    const existing = window.dashboardExistingCharts[chartKey];
    if (existing) {
        if (!force) {
            return;
        }
        existing.destroy();
    }

    const ctx = canvas.getContext('2d');
    const datasets = config.dataset(payload).map(cloneDataset);

    window.dashboardExistingCharts[chartKey] = new Chart(ctx, {
        type: config.type,
        data: {
            labels: payload.labels || [],
            datasets,
        },
        options: config.options || {},
    });
};

const prepareChartObserver = () => {
    if (!Array.isArray(window.dashboardVisibleCharts) || window.dashboardVisibleCharts.length === 0) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        window.dashboardVisibleCharts.forEach((key) => renderChart(key, true));
        return;
    }

    if (chartObserver) {
        chartObserver.disconnect();
    }

    chartObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const key = entry.target.getAttribute('data-card-key');
                if (key) {
                    renderChart(key, true);
                    chartObserver?.unobserve(entry.target);
                }
            }
        });
    }, { threshold: 0.25 });

    window.dashboardVisibleCharts.forEach((key) => {
        const card = document.querySelector(`[data-card-key="${key}"]`);
        if (card) {
            chartObserver.observe(card);
        } else {
            renderChart(key, true);
        }
    });
};

const initSortable = () => {
    // Always destroy existing instances first
    if (metricsSortable) {
        try {
            metricsSortable.destroy();
        } catch (e) {
            // Ignore errors on destroy
        }
        metricsSortable = null;
    }
    if (chartsSortable) {
        try {
            chartsSortable.destroy();
        } catch (e) {
            // Ignore errors on destroy
        }
        chartsSortable = null;
    }

    // Get containers
    const metricsContainer = document.getElementById('metrics-container');
    const chartsContainer = document.getElementById('charts-container');

    if (!metricsContainer && !chartsContainer) {
        return;
    }

    const sortableOptions = (container) => ({
        animation: 300,
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        swapThreshold: 0.65,
        filter: 'button, input, .no-drag',
        preventOnFilter: false,
        onStart(evt) {
            evt.item.style.opacity = '0.5';
            evt.item.style.cursor = 'grabbing';
        },
        onEnd(evt) {
            evt.item.style.opacity = '';
            evt.item.style.cursor = '';

            // Get new order from DOM
            const cardKeys = [];
            Array.from(container.children).forEach((card) => {
                const key = card.getAttribute('data-card-key');
                if (key) {
                    cardKeys.push(key);
                }
            });

            if (cardKeys.length > 0) {
                window.Livewire?.first()?.updateCardOrder(cardKeys);
            }
        },
    });

    if (metricsContainer) {
        // Check customization state from DOM
        const firstMetricCard = metricsContainer.querySelector('.dashboard-metric-card');
        const isCustomizingMetrics = firstMetricCard && firstMetricCard.classList.contains('cursor-grab');

        if (isCustomizingMetrics) {
            try {
                metricsSortable = new Sortable(metricsContainer, sortableOptions(metricsContainer));
            } catch (e) {
                console.error('Error initializing metrics sortable:', e);
            }
        }
    }

    if (chartsContainer) {
        const firstChartCard = chartsContainer.querySelector('.dashboard-chart-card');
        const isCustomizingCharts = firstChartCard && firstChartCard.classList.contains('cursor-grab');

        if (isCustomizingCharts) {
            try {
                chartsSortable = new Sortable(chartsContainer, sortableOptions(chartsContainer));
            } catch (e) {
                console.error('Error initializing charts sortable:', e);
            }
        }
    }
};

const scheduleDashboardInit = () => {
    setTimeout(() => {
        prepareChartObserver();
        initSortable();
    }, 250);
};

// Single re-init hook: 'livewire:navigated' fires both after the initial
// page load and after every wire:navigate transition, so it alone covers
// mount + remount for every dashboard sub-page (replaces the old
// livewire:init / livewire:update / morph.updated combo).
document.addEventListener('livewire:navigated', scheduleDashboardInit);

// Re-init after the customization panel is toggled or preferences are reset
// — both dispatched as browser CustomEvents by DashboardPageComponent /
// HasCardPreferences via $this->dispatch(). Registered once at module load,
// same as the livewire:navigated listener above.
document.addEventListener('customization-toggled', scheduleDashboardInit);
document.addEventListener('dashboard-preferences-reset', scheduleDashboardInit);

// Manual "Refresh" action: re-render charts in place with fresh data
// without waiting for a card-visibility change.
document.addEventListener('dashboard:refresh-charts', (event) => {
    if (event?.detail && typeof event.detail === 'object') {
        if (event.detail.visibleKeys) {
            window.dashboardVisibleCharts = event.detail.visibleKeys;
        }
        if (event.detail.data) {
            window.dashboardChartData = event.detail.data;
        }
    }

    (window.dashboardVisibleCharts || []).forEach((key) => renderChart(key, true));
});

// Fired by <x-dashboard.chart-bootstrap> right after it sets
// window.dashboardChartData / window.dashboardVisibleCharts.
document.addEventListener('dashboard:bootstrap', scheduleDashboardInit);
