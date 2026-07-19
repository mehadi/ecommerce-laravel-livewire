{{--
    Sub-navigation between the 5 dashboard pages. Flux has no tab component
    installed in this app, so this uses the flux:navbar / flux:navbar.item
    components directly as a horizontal tab strip.
--}}
<flux:navbar scrollable class="-mb-px">
    <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
        {{ __('Overview') }}
    </flux:navbar.item>
    <flux:navbar.item :href="route('dashboard.sales')" :current="request()->routeIs('dashboard.sales')" wire:navigate>
        {{ __('Sales & Revenue') }}
    </flux:navbar.item>
    <flux:navbar.item :href="route('dashboard.orders')" :current="request()->routeIs('dashboard.orders')" wire:navigate>
        {{ __('Orders & Fulfillment') }}
    </flux:navbar.item>
    <flux:navbar.item :href="route('dashboard.customers')" :current="request()->routeIs('dashboard.customers')" wire:navigate>
        {{ __('Customers') }}
    </flux:navbar.item>
    <flux:navbar.item :href="route('dashboard.products')" :current="request()->routeIs('dashboard.products')" wire:navigate>
        {{ __('Products') }}
    </flux:navbar.item>
    <flux:navbar.item :href="route('dashboard.profitability')" :current="request()->routeIs('dashboard.profitability')" wire:navigate>
        {{ __('Profitability') }}
    </flux:navbar.item>
    <flux:navbar.item :href="route('dashboard.inventory')" :current="request()->routeIs('dashboard.inventory')" wire:navigate>
        {{ __('Inventory') }}
    </flux:navbar.item>
</flux:navbar>
