<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Analytics') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('System-wide MRR, growth, and revenue trends') }}
            </flux:text>
        </div>
        <div class="flex items-end gap-3">
            <flux:field>
                <flux:label>{{ __('From') }}</flux:label>
                <flux:input type="date" wire:model.live="startDate" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('To') }}</flux:label>
                <flux:input type="date" wire:model.live="endDate" />
            </flux:field>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('MRR (plan-price estimate)') }}</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($this->mrr, 2) }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active Tenants') }}</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $activeTenantCount }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Churn Signals (range)') }}</p>
            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ array_sum($this->churnData['data']) }}</p>
        </div>
    </div>

    <div id="charts-container" class="grid gap-6 lg:grid-cols-2">
        <x-dashboard.chart-card card-key="platform_growth_chart" :card="['title' => __('Tenant Growth')]" />
        <x-dashboard.chart-card card-key="plan_breakdown_chart" :card="['title' => __('Tenants & MRR by Plan')]" />
        <x-dashboard.chart-card card-key="platform_payments_chart" :card="['title' => __('Recorded Payments Over Time')]" />
        <x-dashboard.chart-card card-key="platform_churn_chart" :card="['title' => __('Churn Signals Over Time')]" />
    </div>

    <x-dashboard.chart-bootstrap
        :chart-data="$this->chartDataBundle"
        :visible-charts="array_keys($this->chartDataBundle)"
    />
</div>
