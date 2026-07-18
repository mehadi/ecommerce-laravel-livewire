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
            <div class="flex items-center gap-4">
                <flux:heading size="xl">{{ __('Orders & Fulfillment') }}</flux:heading>
                @include('components.dashboard.toolbar-actions')
            </div>

            @include('components.dashboard.filters')
        </div>

        <x-dashboard.sub-nav />

        <!-- Customization Panel -->
        @if($isCustomizing)
            <div class="relative overflow-hidden rounded-2xl border border-blue-200/50 bg-white shadow-xl ring-1 ring-blue-100 dark:border-blue-800/50 dark:bg-zinc-900 dark:ring-blue-900/20">
                <!-- Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-indigo-50/30 to-purple-50/20 dark:from-blue-950/30 dark:via-indigo-950/20 dark:to-purple-950/10"></div>

                <!-- Content -->
                <div class="relative p-6">
                    <!-- Header -->
                    <div class="mb-6 flex flex-col items-start justify-between gap-4 border-b border-zinc-200/80 pb-5 dark:border-zinc-700/80 md:flex-row md:items-center">
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg ring-1 ring-blue-600/20">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ __('Customize Dashboard') }}</h3>
                                <p class="mt-1.5 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Select visible cards and drag to rearrange. Changes save automatically.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="resetDashboardPreferences" size="sm" variant="outline" class="whitespace-nowrap">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 10-16 0m16 8a8 8 0 10-16 0" />
                                </svg>
                                {{ __('Reset to defaults') }}
                            </flux:button>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <flux:callout class="mb-6" icon="information-circle" variant="info">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('How to customize your dashboard:') }}</p>
                        <ul class="mt-2 space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                                <span>{{ __('Drag cards up or down to reorder them') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                                <span>{{ __('Toggle checkboxes to show or hide cards') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                                <span>{{ __('Changes are saved automatically') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                                <span>{{ __('Use "Reset to defaults" to restore the original layout') }}</span>
                            </li>
                        </ul>
                    </flux:callout>

                    <!-- Insights Section -->
                    <div class="mb-8">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100 dark:bg-cyan-900/30">
                                <svg class="h-4 w-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Insight Cards') }}</h4>
                            <div class="flex-1 border-t border-zinc-200 dark:border-zinc-700"></div>
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ count($this->availableInsightCards) }} {{ __('available') }}</span>
                        </div>
                        <flux:checkbox.group class="flex flex-wrap gap-3">
                            @foreach($this->availableInsightCards as $key => $insight)
                                @php
                                    $pref = $this->userPreferences->firstWhere(fn($p) => $p->card_key === $key && $p->card_type === 'insight');
                                    $isVisible = $pref?->is_visible ?? true;
                                @endphp
                                <div class="group relative flex min-w-0 flex-1 basis-48 rounded-xl border bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-zinc-900 {{ $isVisible ? 'border-cyan-300 bg-cyan-50/80 ring-1 ring-cyan-200/50 dark:border-cyan-700 dark:bg-cyan-900/20 dark:ring-cyan-800/30' : 'border-zinc-200 dark:border-zinc-700' }}">
                                    <flux:checkbox
                                        wire:click.stop="toggleCardVisibility('{{ $key }}')"
                                        :checked="$isVisible"
                                        label="{{ $insight['title'] }}"
                                    />
                                </div>
                            @endforeach
                        </flux:checkbox.group>
                    </div>

                    <!-- Metrics Section -->
                    <div class="mb-8">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                                <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Metric Cards') }}</h4>
                            <div class="flex-1 border-t border-zinc-200 dark:border-zinc-700"></div>
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ count($this->availableMetricCards) }} {{ __('available') }}</span>
                        </div>
                        <flux:checkbox.group class="flex flex-wrap gap-3">
                            @foreach($this->availableMetricCards as $key => $card)
                                @php
                                    $pref = $this->userPreferences->firstWhere(fn($p) => $p->card_key === $key && $p->card_type === 'metric');
                                    $isVisible = $pref?->is_visible ?? true;
                                @endphp
                                <div class="group relative flex min-w-0 flex-1 basis-48 rounded-xl border bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-zinc-900 {{ $isVisible ? 'border-emerald-300 bg-emerald-50/80 ring-1 ring-emerald-200/50 dark:border-emerald-700 dark:bg-emerald-900/20 dark:ring-emerald-800/30' : 'border-zinc-200 dark:border-zinc-700' }}">
                                    <flux:checkbox
                                        wire:click.stop="toggleCardVisibility('{{ $key }}')"
                                        :checked="$isVisible"
                                        label="{{ $card['title'] }}"
                                    />
                                </div>
                            @endforeach
                        </flux:checkbox.group>
                    </div>

                    <!-- Charts Section -->
                    <div>
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                                <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Chart Cards') }}</h4>
                            <div class="flex-1 border-t border-zinc-200 dark:border-zinc-700"></div>
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ count($this->availableChartCards) }} {{ __('available') }}</span>
                        </div>
                        <flux:checkbox.group class="flex flex-wrap gap-3">
                            @foreach($this->availableChartCards as $key => $chart)
                                @php
                                    $pref = $this->userPreferences->firstWhere(fn($p) => $p->card_key === $key && $p->card_type === 'chart');
                                    $isVisible = $pref?->is_visible ?? true;
                                @endphp
                                <div class="group relative flex min-w-0 flex-1 basis-48 rounded-xl border bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-zinc-900 {{ $isVisible ? 'border-indigo-300 bg-indigo-50/80 ring-1 ring-indigo-200/50 dark:border-indigo-700 dark:bg-indigo-900/20 dark:ring-indigo-800/30' : 'border-zinc-200 dark:border-zinc-700' }}">
                                    <flux:checkbox
                                        wire:click.stop="toggleCardVisibility('{{ $key }}')"
                                        :checked="$isVisible"
                                        label="{{ $chart['title'] }}"
                                    />
                                </div>
                            @endforeach
                        </flux:checkbox.group>
                    </div>
                </div>
            </div>
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
                                        <span class="font-medium text-zinc-700 dark:text-zinc-200">৳{{ number_format($summary['revenue'], 2) }}</span>
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
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">৳{{ number_format((float) $order->total, 2) }}</p>
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

        <x-dashboard.chart-bootstrap
            :chart-data="$this->chartDataBundle()"
            :visible-charts="$this->orderedChartCards->pluck('key')->toArray()"
        />
    </div>
</div>
