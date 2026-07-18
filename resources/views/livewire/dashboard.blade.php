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
                <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
                <flux:button wire:click="toggleCustomization" variant="{{ $isCustomizing ? 'primary' : 'ghost' }}" size="sm">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>{{ $isCustomizing ? __('Done') : __('Customize') }}</span>
                    </span>
                </flux:button>
                <flux:button wire:click="refreshDashboard" variant="ghost" size="sm" wire:loading.attr="disabled" wire:target="refreshDashboard">
                    <span wire:loading.remove wire:target="refreshDashboard" class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>{{ __('Refresh') }}</span>
                    </span>
                    <span wire:loading wire:target="refreshDashboard" class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('Refreshing...') }}</span>
                    </span>
                </flux:button>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <!-- Date Range Presets -->
                <flux:field>
                    <flux:select wire:model.live="dateRange">
                        <option value="7">{{ __('Last 7 Days') }}</option>
                        <option value="30">{{ __('Last 30 Days') }}</option>
                        <option value="90">{{ __('Last 90 Days') }}</option>
                        <option value="365">{{ __('Last Year') }}</option>
                        <option value="custom">{{ __('Custom Range') }}</option>
                    </flux:select>
                </flux:field>
                
                <!-- Custom Date Range -->
                @if($dateRange === 'custom')
                    <flux:field>
                        <flux:input type="date" wire:model.live="startDate" />
                    </flux:field>
                    <flux:field>
                        <flux:input type="date" wire:model.live="endDate" />
                    </flux:field>
                @endif
                
                <!-- Status Filter -->
                <flux:field>
                    <flux:select wire:model.live="statusFilter">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="confirmed">{{ __('Confirmed') }}</option>
                        <option value="processing">{{ __('Processing') }}</option>
                        <option value="shipped">{{ __('Shipped') }}</option>
                        <option value="delivered">{{ __('Delivered') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </flux:select>
                </flux:field>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2">
            <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" size="sm">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>{{ __('Products') }}</span>
                </span>
            </flux:button>
            <flux:button :href="route('admin.orders.index')" wire:navigate variant="ghost" size="sm">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>{{ __('Orders') }}</span>
                </span>
            </flux:button>
            <flux:button :href="route('admin.products.create')" wire:navigate variant="ghost" size="sm">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('Add Product') }}</span>
                </span>
            </flux:button>
            <flux:button :href="route('admin.categories.index')" wire:navigate variant="ghost" size="sm">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>{{ __('Categories') }}</span>
                </span>
            </flux:button>
        </div>

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

        <!-- Dashboard Insights -->
        @if(count($this->dashboardInsights) > 0)
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($this->dashboardInsights as $index => $insight)
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

        @php
            $statusSummary = $this->statusSummary;
            $recentOrders = $this->recentOrders;
        @endphp

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

        <!-- Metrics Cards - Dynamic & Draggable -->
        @if($this->orderedMetricCards->count() > 0)
            <div 
                id="metrics-container" 
                class="grid gap-4 md:grid-cols-2 lg:grid-cols-4"
            >
                @foreach($this->orderedMetricCards as $card)
                    <x-dashboard.metric-card 
                        :card-key="$card['key']" 
                        :card="$card"
                        :is-customizing="$isCustomizing"
                        :value="$this->getMetricValue($card['key'])"
                        :subtitle="$this->getMetricSubtitle($card['key'])"
                    />
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">{{ __('No metric cards visible') }}</p>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enable metric cards in customization mode to view your key metrics.') }}</p>
                <flux:button wire:click="toggleCustomization" variant="primary" size="sm" class="mt-4">
                    {{ __('Customize Dashboard') }}
                </flux:button>
            </div>
        @endif

        <!-- Charts Grid - Dynamic -->
        @if($this->orderedChartCards->count() > 0)
            <div 
                id="charts-container" 
                class="grid gap-6 lg:grid-cols-2"
            >
                @foreach($this->orderedChartCards as $card)
                    <x-dashboard.chart-card 
                        :card-key="$card['key']" 
                        :card="$card"
                        :is-customizing="$isCustomizing"
                    />
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">{{ __('No chart cards visible') }}</p>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enable chart cards in customization mode to view your analytics.') }}</p>
                <flux:button wire:click="toggleCustomization" variant="primary" size="sm" class="mt-4">
                    {{ __('Customize Dashboard') }}
                </flux:button>
            </div>
        @endif

            <!-- Low Stock Products Alert -->
            @if($this->lowStockProducts->count() > 0)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 dark:border-amber-800 dark:bg-amber-900/20">
                    <h3 class="mb-4 text-lg font-semibold text-amber-900 dark:text-amber-100">{{ __('Low Stock Alert') }}</h3>
                    <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-5">
                        @foreach($this->lowStockProducts as $product)
                            <div class="rounded-lg border border-amber-200 bg-white p-3 dark:border-amber-800 dark:bg-zinc-900">
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ $product->name_en }}</p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Stock') }}: <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $product->getSyncedStock() }}</span></p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @once
            <!-- Chart.js CDN -->
            <script
                id="dashboard-chartjs"
                src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
                onload="document.dispatchEvent(new Event('dashboard:chartjs-ready'))"
            ></script>
            <!-- SortableJS CDN -->
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        @endonce

        <style>
        .sortable-ghost {
            opacity: 0.4;
            transform: scale(0.95);
        }
        .sortable-chosen {
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            z-index: 50;
        }
        .sortable-drag {
            cursor: grabbing !important;
        }
        .dragging {
            cursor: grabbing !important;
        }
        </style>

        <script>
        window.dashboardChartData = @json($this->chartDataBundle());
        window.dashboardVisibleCharts = @json($this->orderedChartCards->pluck('key')->toArray());

        if (window.dashboardExistingCharts) {
            Object.values(window.dashboardExistingCharts).forEach((chart) => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
        }
        window.dashboardExistingCharts = {};

        let chartObserver = null;
        let metricsSortable = null;
        let chartsSortable = null;

        const chartConfigs = {
            revenue_chart: {
                type: 'line',
                dataset: (data) => [{
                    label: '{{ __('Revenue') }}',
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
                    label: '{{ __('Orders') }}',
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
                    label: '{{ __('Revenue') }}',
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
                    label: '{{ __('Revenue') }}',
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
                        label: '{{ __('Orders') }}',
                        data: data.orders,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        label: '{{ __('Revenue') }}',
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
                    label: '{{ __('Revenue') }}',
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
                    label: '{{ __('Revenue') }}',
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
                    label: '{{ __('Orders') }}',
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
                        label: '{{ __('Order Count') }}',
                        data: data.count,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        label: '{{ __('Avg. Order Value') }}',
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
            if (typeof Chart === 'undefined') {
                return;
            }

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

            if (typeof Chart === 'undefined') {
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

        const runAfterChartReady = (callback) => {
            if (typeof Chart !== 'undefined') {
                callback();
                return;
            }

            const handler = () => {
                document.removeEventListener('dashboard:chartjs-ready', handler);
                callback();
            };

            document.addEventListener('dashboard:chartjs-ready', handler, { once: true });
        };

        const scheduleDashboardInit = () => {
            setTimeout(() => {
                runAfterChartReady(() => {
                    prepareChartObserver();
                    initSortable();
                });
            }, 250);
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

            // Check if SortableJS is available
            if (typeof Sortable === 'undefined') {
                return;
            }

            // Get containers
            const metricsContainer = document.getElementById('metrics-container');
            const chartsContainer = document.getElementById('charts-container');
            
            if (!metricsContainer || !chartsContainer) {
                return;
            }

            // Check customization state from DOM
            const firstCard = metricsContainer.querySelector('.dashboard-metric-card');
            const isCustomizing = firstCard && firstCard.classList.contains('cursor-grab');

            if (isCustomizing) {
                // Metrics Sortable - entire card is draggable
                try {
                    metricsSortable = new Sortable(metricsContainer, {
                        animation: 300,
                        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        swapThreshold: 0.65,
                        filter: 'button, input, .no-drag',
                        preventOnFilter: false,
                        onStart: function(evt) {
                            evt.item.style.opacity = '0.5';
                            evt.item.style.cursor = 'grabbing';
                        },
                        onEnd: function(evt) {
                            evt.item.style.opacity = '';
                            evt.item.style.cursor = '';
                            
                            // Get new order from DOM
                            const cardKeys = [];
                            Array.from(metricsContainer.children).forEach(card => {
                                const key = card.getAttribute('data-card-key');
                                if (key) {
                                    cardKeys.push(key);
                                }
                            });
                            
                            if (cardKeys.length > 0) {
                                @this.updateCardOrder(cardKeys);
                            }
                        }
                    });
                } catch (e) {
                    console.error('Error initializing metrics sortable:', e);
                }

                // Charts Sortable - entire card is draggable
                try {
                    chartsSortable = new Sortable(chartsContainer, {
                        animation: 300,
                        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        swapThreshold: 0.65,
                        filter: 'button, input, .no-drag',
                        preventOnFilter: false,
                        onStart: function(evt) {
                            evt.item.style.opacity = '0.5';
                            evt.item.style.cursor = 'grabbing';
                        },
                        onEnd: function(evt) {
                            evt.item.style.opacity = '';
                            evt.item.style.cursor = '';
                            
                            // Get new order from DOM
                            const cardKeys = [];
                            Array.from(chartsContainer.children).forEach(card => {
                                const key = card.getAttribute('data-card-key');
                                if (key) {
                                    cardKeys.push(key);
                                }
                            });
                            
                            if (cardKeys.length > 0) {
                                @this.updateCardOrder(cardKeys);
                            }
                        }
                    });
                } catch (e) {
                    console.error('Error initializing charts sortable:', e);
                }
            }
        };

        document.addEventListener('livewire:init', () => {
            // Initialize on mount
            setTimeout(() => {
                initSortable();
                runAfterChartReady(prepareChartObserver);
            }, 400);

            Livewire.on('dashboard-preferences-reset', () => {
                setTimeout(() => {
                    initSortable();
                    runAfterChartReady(prepareChartObserver);
                }, 400);
            });

            Livewire.on('customization-toggled', () => {
                scheduleDashboardInit();
            });

            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    initSortable();
                    runAfterChartReady(prepareChartObserver);
                }, 300);
            });
        });

        document.addEventListener('livewire:update', () => {
            scheduleDashboardInit();
        });

        document.addEventListener('dashboard:refresh-charts', (event) => {
            if (event?.detail && typeof event.detail === 'object') {
                if (event.detail.visibleKeys) {
                    window.dashboardVisibleCharts = event.detail.visibleKeys;
                }
                if (event.detail.data) {
                    window.dashboardChartData = event.detail.data;
                }
            }

            runAfterChartReady(() => {
                window.dashboardVisibleCharts.forEach((key) => renderChart(key, true));
            });
        });

        // Initial kick-off
        scheduleDashboardInit();
        </script>
</div>
