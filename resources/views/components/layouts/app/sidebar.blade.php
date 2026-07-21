<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable collapsible class="border-e border-zinc-200 bg-gradient-to-b from-zinc-50 to-zinc-100/60 dark:border-zinc-800 dark:from-zinc-900 dark:to-zinc-950">
            <div class="flex items-center gap-1">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <flux:sidebar.brand :name="\App\Models\Setting::get('site_name', config('app.name'))" :href="route('dashboard')" wire:navigate class="flex-1 rounded-xl bg-white/70 shadow-soft ring-1 ring-black/5 dark:bg-white/5 dark:ring-white/10 in-data-flux-sidebar-collapsed-desktop:bg-transparent in-data-flux-sidebar-collapsed-desktop:shadow-none in-data-flux-sidebar-collapsed-desktop:ring-0">
                    <x-slot name="logo">
                        <div class="flex aspect-square size-6 items-center justify-center rounded-md bg-gradient-to-br from-accent-content to-accent-content/70 text-accent-foreground shadow-sm">
                            <x-app-logo-icon class="size-3.5 fill-current text-white dark:text-black" />
                        </div>
                    </x-slot>
                </flux:sidebar.brand>

                <flux:sidebar.collapse class="max-lg:hidden" />
            </div>

            @if(\App\Support\Tenancy::check())
                <flux:sidebar.group expandable heading="{{ __('Store') }}" icon="home-modern">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard*')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="globe-alt" :href="route('admin.website.index')" :current="request()->routeIs('admin.website.*')" wire:navigate>{{ __('Website Settings') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="bars-3" :href="route('admin.navigation.index')" :current="request()->routeIs('admin.navigation.*')" wire:navigate>{{ __('Navigation Settings') }}</flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group expandable heading="{{ __('Catalog') }}" icon="cube">
                    <flux:sidebar.item icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate>{{ __('Products') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>{{ __('Categories') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="view-columns" :href="route('admin.categories-display.index')" :current="request()->routeIs('admin.categories-display.*')" wire:navigate>{{ __('Categories Display') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="squares-2x2" :href="route('admin.attributes.index')" :current="request()->routeIs('admin.attributes.*')" wire:navigate>{{ __('Attributes') }}</flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group expandable heading="{{ __('Inventory') }}" icon="archive-box">
                    <flux:sidebar.item icon="archive-box" :href="route('admin.inventory.index')" :current="request()->routeIs('admin.inventory.*')" wire:navigate>{{ __('Inventory') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="building-storefront" :href="route('admin.warehouses.index')" :current="request()->routeIs('admin.warehouses.*')" wire:navigate>{{ __('Warehouses') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="arrows-right-left" :href="route('admin.stock-transfers.index')" :current="request()->routeIs('admin.stock-transfers.*')" wire:navigate>{{ __('Stock Transfers') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('admin.suppliers.index')" :current="request()->routeIs('admin.suppliers.*')" wire:navigate>{{ __('Suppliers') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.purchase-orders.index')" :current="request()->routeIs('admin.purchase-orders.*')" wire:navigate>{{ __('Purchase Orders') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-check" :href="route('admin.cycle-counts.index')" :current="request()->routeIs('admin.cycle-counts.*')" wire:navigate>{{ __('Cycle Counts') }}</flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group expandable heading="{{ __('Sales') }}" icon="shopping-bag">
                    <flux:sidebar.item icon="shopping-bag" :href="route('admin.orders.index')" :current="request()->routeIs('admin.orders.*')" wire:navigate>{{ __('Orders') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="ticket" :href="route('admin.coupons.index')" :current="request()->routeIs('admin.coupons.*')" wire:navigate>{{ __('Coupons') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('admin.shipping.index')" :current="request()->routeIs('admin.shipping.*')" wire:navigate>{{ __('Shipping') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="rectangle-group" :href="route('admin.cart-checkout.index')" :current="request()->routeIs('admin.cart-checkout.*')" wire:navigate>{{ __('Cart & Checkout') }}</flux:sidebar.item>
                </flux:sidebar.group>

                @canany(['access pos', 'view pos reports', 'manage pos registers', 'process pos refunds'])
                    <flux:sidebar.group expandable heading="{{ __('POS') }}" icon="calculator">
                        @can('access pos')
                            <flux:sidebar.item icon="calculator" :href="route('pos.terminal')" wire:navigate>{{ __('Open Till') }}</flux:sidebar.item>
                        @endcan
                        @can('view pos reports')
                            <flux:sidebar.item icon="chart-bar" :href="route('admin.pos.dashboard')" :current="request()->routeIs('admin.pos.dashboard')" wire:navigate>{{ __('POS Dashboard') }}</flux:sidebar.item>
                            <flux:sidebar.item icon="clock" :href="route('admin.pos.shifts.index')" :current="request()->routeIs('admin.pos.shifts.*')" wire:navigate>{{ __('Shifts') }}</flux:sidebar.item>
                        @endcan
                        @can('process pos refunds')
                            <flux:sidebar.item icon="receipt-refund" :href="route('admin.pos.refunds.index')" :current="request()->routeIs('admin.pos.refunds.*')" wire:navigate>{{ __('Refunds') }}</flux:sidebar.item>
                        @endcan
                        @can('manage pos registers')
                            <flux:sidebar.item icon="building-storefront" :href="route('admin.pos.registers.index')" :current="request()->routeIs('admin.pos.registers.*')" wire:navigate>{{ __('Registers') }}</flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>
                @endcanany

                <flux:sidebar.group expandable heading="{{ __('Storefront') }}" icon="swatch">
                    <flux:sidebar.item icon="squares-2x2" :href="route('admin.sections.index')" :current="request()->routeIs('admin.sections.*')" wire:navigate>{{ __('Sections') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.testimonials.index')" :current="request()->routeIs('admin.testimonials.*')" wire:navigate>{{ __('Testimonials') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="document-duplicate" :href="route('admin.landing-pages.index')" :current="request()->routeIs('admin.landing-pages.*')" wire:navigate>{{ __('Landing Pages') }}</flux:sidebar.item>
                </flux:sidebar.group>

                @canany(['manage users', 'manage roles'])
                    <flux:sidebar.group expandable heading="{{ __('Administration') }}" icon="shield-check">
                        @can('manage users')
                            <flux:sidebar.item icon="user-group" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>{{ __('Users') }}</flux:sidebar.item>
                        @endcan
                        @can('manage roles')
                            <flux:sidebar.item icon="shield-check" :href="route('admin.roles.index')" :current="request()->routeIs('admin.roles.*')" wire:navigate>{{ __('Roles') }}</flux:sidebar.item>
                            <flux:sidebar.item icon="key" :href="route('admin.permissions.index')" :current="request()->routeIs('admin.permissions.*')" wire:navigate>{{ __('Permissions') }}</flux:sidebar.item>
                        @endcan
                        <flux:sidebar.item icon="credit-card" :href="route('admin.billing.index')" :current="request()->routeIs('admin.billing.*')" wire:navigate>{{ __('Billing') }}</flux:sidebar.item>
                    </flux:sidebar.group>
                @endcanany
            @endif

            @can('access platform')
                <flux:sidebar.group expandable heading="{{ __('Platform') }}" icon="building-office-2">
                    @foreach (config('platform_nav', []) as $item)
                        @continue(isset($item['permission']) && ! auth()->user()->can($item['permission']))
                        <flux:sidebar.item icon="{{ $item['icon'] }}" :href="route($item['route'])" :current="request()->routeIs($item['route_pattern'])" wire:navigate>{{ __($item['label']) }}</flux:sidebar.item>
                    @endforeach
                </flux:sidebar.group>
            @endcan

            <flux:sidebar.spacer />

            {{--<flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>--}}

            <!-- Theme Toggle -->
            <div class="flex items-center justify-center rounded-2xl bg-white/70 p-1 shadow-soft ring-1 ring-black/5 dark:bg-white/5 dark:ring-white/10">
                <x-layouts.app.theme-toggle position="right" />
            </div>

            <!-- Notifications -->
            @auth
                <div class="flex items-center justify-center rounded-2xl bg-white/70 p-1 shadow-soft ring-1 ring-black/5 dark:bg-white/5 dark:ring-white/10">
                    <livewire:notification-bell />
                </div>
            @endauth

            <!-- Desktop User Menu -->
            @auth
                <div class="rounded-2xl bg-white/70 p-1 shadow-soft ring-1 ring-black/5 dark:bg-white/5 dark:ring-white/10 in-data-flux-sidebar-collapsed-desktop:bg-transparent in-data-flux-sidebar-collapsed-desktop:p-0 in-data-flux-sidebar-collapsed-desktop:shadow-none in-data-flux-sidebar-collapsed-desktop:ring-0">
                    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                        <flux:sidebar.profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon:trailing="chevrons-up-down"
                        />

                        <x-layouts.app.user-menu />
                    </flux:dropdown>
                </div>
            @endauth
        </flux:sidebar>

        <!-- Mobile User Menu -->
        @auth
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <flux:spacer />

                <x-layouts.app.theme-toggle position="bottom" />

                <livewire:notification-bell />

                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <x-layouts.app.user-menu />
                </flux:dropdown>
            </flux:header>
        @endauth

        @if(session()->has('impersonator_id'))
            <flux:callout variant="warning" class="rounded-none">
                {{ __('You are impersonating :name.', ['name' => auth()->user()->name]) }}
                <form method="POST" action="{{ route('impersonation.stop') }}" class="inline">
                    @csrf
                    <flux:button as="button" type="submit" variant="ghost" size="sm" class="underline">
                        {{ __('Return to Platform') }}
                    </flux:button>
                </form>
            </flux:callout>
        @endif

        {{ $slot }}

        @fluxScripts
    </body>
</html>
