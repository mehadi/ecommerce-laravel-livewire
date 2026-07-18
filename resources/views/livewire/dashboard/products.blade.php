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
            <flux:heading size="xl">{{ __('Products') }}</flux:heading>

            @include('components.dashboard.toolbar-actions')
        </div>

        <x-dashboard.sub-nav />

        <div class="flex flex-wrap gap-3">
            @include('components.dashboard.filters')
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
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">&bull;</span>
                                <span>{{ __('Drag cards up or down to reorder them') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">&bull;</span>
                                <span>{{ __('Toggle checkboxes to show or hide cards') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">&bull;</span>
                                <span>{{ __('Changes are saved automatically') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-blue-600 dark:text-blue-400">&bull;</span>
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

        <x-dashboard.card-grid
            :metrics="$this->orderedMetricCards->map(fn ($card) => array_merge($card, [
                'value' => $this->getMetricValue($card['key']),
                'subtitle' => $this->getMetricSubtitle($card['key']),
            ]))"
            :charts="$this->orderedChartCards"
            :insights="$this->dashboardInsights"
            :is-customizing="$isCustomizing"
        />

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

        <x-dashboard.chart-bootstrap
            :chart-data="$this->chartDataBundle()"
            :visible-charts="$this->orderedChartCards->pluck('key')->toArray()"
        />
    </div>
</div>
