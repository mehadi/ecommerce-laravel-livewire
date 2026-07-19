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
                    <flux:heading size="xl">{{ __('Overview') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-1">
                        {{ __('A high-level snapshot of your store\'s key performance metrics.') }}
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
                <x-dashboard.customize-section
                    icon-path='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'
                    color="emerald"
                    :title="__('Metric Cards')"
                    :items="$this->availableMetricCards"
                    type="metric"
                    :user-preferences="$this->userPreferences"
                />
            </x-dashboard.customize-panel>
        @endif

        <x-dashboard.card-grid
            :metrics="$this->orderedMetricCards->map(fn ($card) => array_merge($card, [
                'value' => $this->getMetricValue($card['key']),
                'subtitle' => $this->getMetricSubtitle($card['key']),
            ]))"
            :is-customizing="$isCustomizing"
        />

        <x-dashboard.chart-bootstrap />
    </div>
</div>
