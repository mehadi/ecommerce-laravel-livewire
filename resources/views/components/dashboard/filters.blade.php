{{--
    Date-range + status filter controls, extracted verbatim from the
    monolith dashboard blade. This is a plain Blade partial meant to be
    pulled in with @include('components.dashboard.filters') from any of the
    5 dashboard page views — it shares the including view's variable scope,
    so it binds directly to the host Livewire component's $dateRange,
    $startDate, $endDate, $statusFilter properties (all provided by the
    HasDashboardFilters trait) via wire:model.live. No props required.
--}}
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
