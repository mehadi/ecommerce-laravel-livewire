{{--
    Customize / Refresh toolbar buttons, extracted verbatim from the
    monolith dashboard blade. Plain Blade partial meant to be pulled in with
    @include('components.dashboard.toolbar-actions') from any of the 5
    dashboard page views — it shares the including view's scope, so it
    binds directly to the host component's $isCustomizing property and its
    toggleCustomization()/refreshDashboard() methods (both on
    DashboardPageComponent / HasCardPreferences). No props required.
--}}
<flux:button wire:click="toggleCustomization" variant="{{ $isCustomizing ? 'primary' : 'ghost' }}" size="sm" wire:loading.attr="disabled" wire:target="toggleCustomization">
    <span wire:loading.remove wire:target="toggleCustomization" class="inline-flex items-center gap-1.5">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span>{{ $isCustomizing ? __('Done') : __('Customize') }}</span>
    </span>
    <span wire:loading wire:target="toggleCustomization" class="inline-flex items-center gap-1.5">
        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ __('Loading...') }}</span>
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
