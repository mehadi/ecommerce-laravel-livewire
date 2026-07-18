<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-1">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <flux:sidebar.brand :name="config('app.name')" :href="route('dashboard')" wire:navigate class="flex-1 rounded-xl bg-white/70 shadow-soft dark:bg-white/5 in-data-flux-sidebar-collapsed-desktop:bg-transparent in-data-flux-sidebar-collapsed-desktop:shadow-none">
                    <x-slot name="logo">
                        <div class="flex aspect-square size-6 items-center justify-center rounded-md bg-gradient-to-br from-accent-content to-accent-content/70 text-accent-foreground">
                            <x-app-logo-icon class="size-3.5 fill-current text-white dark:text-black" />
                        </div>
                    </x-slot>
                </flux:sidebar.brand>

                <flux:sidebar.collapse class="max-lg:hidden" />
            </div>

            <flux:sidebar.group expandable heading="{{ __('Platform') }}" icon="home-modern">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
                <flux:sidebar.item icon="globe-alt" :href="route('admin.website.index')" :current="request()->routeIs('admin.website.*')" wire:navigate>{{ __('Website Settings') }}</flux:sidebar.item>
                <flux:sidebar.item icon="bars-3" :href="route('admin.navigation.index')" :current="request()->routeIs('admin.navigation.*')" wire:navigate>{{ __('Navigation Settings') }}</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group expandable heading="{{ __('Ecommerce') }}" icon="shopping-cart">
                <flux:sidebar.item icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate>{{ __('Products') }}</flux:sidebar.item>
                <flux:sidebar.item icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>{{ __('Categories') }}</flux:sidebar.item>
                <flux:sidebar.item icon="shopping-bag" :href="route('admin.orders.index')" :current="request()->routeIs('admin.orders.*')" wire:navigate>{{ __('Orders') }}</flux:sidebar.item>
                <flux:sidebar.item icon="ticket" :href="route('admin.coupons.index')" :current="request()->routeIs('admin.coupons.*')" wire:navigate>{{ __('Coupons') }}</flux:sidebar.item>
                <flux:sidebar.item icon="truck" :href="route('admin.shipping.index')" :current="request()->routeIs('admin.shipping.*')" wire:navigate>{{ __('Shipping') }}</flux:sidebar.item>
                <flux:sidebar.item icon="rectangle-group" :href="route('admin.cart-checkout.index')" :current="request()->routeIs('admin.cart-checkout.*')" wire:navigate>{{ __('Cart & Checkout') }}</flux:sidebar.item>
                <flux:sidebar.item icon="squares-2x2" :href="route('admin.sections.index')" :current="request()->routeIs('admin.sections.*')" wire:navigate>{{ __('Sections') }}</flux:sidebar.item>
                <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.testimonials.index')" :current="request()->routeIs('admin.testimonials.*')" wire:navigate>{{ __('Testimonials') }}</flux:sidebar.item>
                <flux:sidebar.item icon="document-duplicate" :href="route('admin.landing-pages.index')" :current="request()->routeIs('admin.landing-pages.*')" wire:navigate>{{ __('Landing Pages') }}</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group expandable heading="{{ __('Settings') }}" icon="cog-6-tooth">
                <flux:sidebar.item icon="tag" :href="route('admin.attributes.index')" :current="request()->routeIs('admin.attributes.*')" wire:navigate>{{ __('Attributes') }}</flux:sidebar.item>
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
                </flux:sidebar.group>
            @endcanany

            <flux:sidebar.spacer />

            {{--<flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>--}}

            <!-- Desktop User Menu -->
            @auth
                <div class="rounded-xl bg-white/70 p-1 shadow-soft dark:bg-white/5 in-data-flux-sidebar-collapsed-desktop:bg-transparent in-data-flux-sidebar-collapsed-desktop:p-0 in-data-flux-sidebar-collapsed-desktop:shadow-none">
                    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                        <flux:sidebar.profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon:trailing="chevrons-up-down"
                        />

                        <flux:menu class="w-[220px]">
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </span>

                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            @endauth
        </flux:sidebar>

        <!-- Mobile User Menu -->
        @auth
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <flux:spacer />

                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                        >
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>
        @endauth

        {{ $slot }}

        @fluxScripts
    </body>
</html>
