{{--
    Sub-navigation between the 5 dashboard pages. Flux has no tab component
    installed in this app, so this follows the same flux:navbar /
    flux:navbar.item pattern already used in
    resources/views/components/layouts/app/header.blade.php (lines ~14-18).
--}}
<flux:navbar class="-mb-px">
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
</flux:navbar>
