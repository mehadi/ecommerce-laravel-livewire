{{-- Transparent Overlay: sits see-through on top of the hero image, then
     fades to a solid blurred bar once the visitor scrolls past it. Link/icon
     colors are pre-computed for both states server-side; only the Alpine
     `scrolled` flag decides which literal class string is applied, so no
     class list needs to be built client-side.

     Hero sections are theme-colored (some light, some dark), so the white
     unscrolled text can't assume a dark image is behind it. A dark gradient
     scrim is rendered behind the bar in that state to guarantee contrast no
     matter which hero variant is underneath; it fades out once the header
     switches to its own solid background on scroll. --}}
<header
    x-data="{ mobileMenuOpen: false, searchOpen: false, scrolled: false, cartCount: {{ $cartItemCount }} }"
    x-on:cart-updated.window="cartCount = $event.detail.count"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 40 }, { passive: true })"
    class="fixed top-0 left-0 right-0 z-50 transition-colors duration-300 border-b"
    :class="scrolled || mobileMenuOpen || searchOpen
        ? 'bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md shadow-sm border-zinc-200/70 dark:border-white/10'
        : 'bg-transparent border-transparent'"
    @keydown.escape.window="mobileMenuOpen = false; searchOpen = false"
>
    <div
        x-show="!(scrolled || mobileMenuOpen || searchOpen)"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-x-0 top-0 h-40 sm:h-48 -z-10 bg-gradient-to-b from-black/55 via-black/20 to-transparent"
        aria-hidden="true"
    ></div>

    <div class="container mx-auto frontend-container px-6 sm:px-8 lg:px-10">
        <div class="flex items-center justify-between h-18 sm:h-24">
            <a href="/" wire:navigate class="flex items-center gap-2.5 shrink-0 group focus-visible:outline-none focus-visible:ring-2 rounded-md" :class="scrolled || mobileMenuOpen ? 'focus-visible:ring-zinc-900 dark:focus-visible:ring-white' : 'focus-visible:ring-white'">
                @if($siteLogo)
                    <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" class="h-8 sm:h-9 w-auto">
                @else
                    <span class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-md transition-colors duration-300" :class="scrolled || mobileMenuOpen ? 'bg-zinc-900 dark:bg-white' : 'bg-white'">
                        <span class="font-display font-extrabold text-base sm:text-lg leading-none transition-colors duration-300" :class="scrolled || mobileMenuOpen ? 'text-white dark:text-zinc-900' : 'text-zinc-900'">{{ substr($siteName, 0, 1) }}</span>
                    </span>
                @endif
                <span class="font-display text-base sm:text-lg font-bold tracking-tight whitespace-nowrap transition-colors duration-300" :class="scrolled || mobileMenuOpen ? 'text-zinc-900 dark:text-white' : 'text-white'">{{ $siteName }}</span>
            </a>

            <div class="hidden lg:flex items-center gap-1" x-data="{ openDropdown: null }">
                @foreach($navigationItems as $navItem)
                    @php
                        $hasChildren = $navItem->children && $navItem->children->where('is_active', true)->count() > 0;
                        $children = $hasChildren ? $navItem->children->where('is_active', true)->sortBy('order') : collect();

                        $isActive = match (true) {
                            $navItem->type === 'route' && $navItem->route_name => request()->routeIs($navItem->route_name),
                            $navItem->type === 'section' => request()->url() === url($navItem->url),
                            default => request()->url() === url($navItem->url) || request()->is(parse_url($navItem->url, PHP_URL_PATH)),
                        };
                        foreach ($children as $child) {
                            $childActive = match (true) {
                                $child->type === 'route' && $child->route_name => request()->routeIs($child->route_name),
                                $child->type === 'section' => request()->url() === url($child->url),
                                default => request()->url() === url($child->url) || request()->is(parse_url($child->url, PHP_URL_PATH)),
                            };
                            $isActive = $isActive || $childActive;
                        }

                        $solidClasses = $isActive ? 'text-zinc-900 dark:text-white bg-zinc-100 dark:bg-white/[0.08]' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-white/[0.06]';
                        $onImageClasses = $isActive ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10';
                    @endphp

                    <div class="relative" @if($hasChildren) @mouseenter="openDropdown = {{ $navItem->id }}" @mouseleave="openDropdown = null" @endif>
                        <a href="{{ $navItem->resolved_url }}"
                            wire:navigate
                            @if($navItem->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                            @if($hasChildren) @focus="openDropdown = {{ $navItem->id }}" aria-haspopup="true" :aria-expanded="openDropdown === {{ $navItem->id }} ? 'true' : 'false'" @endif
                            class="block px-3.5 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :class="scrolled ? '{{ $solidClasses }}' : '{{ $onImageClasses }}'">
                            <span class="flex items-center gap-1.5">
                                @if($navItem->icon)
                                    <flux:icon name="{{ $navItem->icon }}" class="w-4 h-4" />
                                @endif
                                {{ $navItem->label }}
                                @if($hasChildren)
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openDropdown === {{ $navItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </span>
                        </a>

                        @if($hasChildren)
                            <div
                                x-show="openDropdown === {{ $navItem->id }}"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                class="absolute top-full left-0 mt-3 w-52 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] overflow-hidden p-1.5 z-50"
                                style="display: none;"
                            >
                                @foreach($children as $child)
                                    @php
                                        $childIsActive = match (true) {
                                            $child->type === 'route' && $child->route_name => request()->routeIs($child->route_name),
                                            $child->type === 'section' => request()->url() === url($child->url),
                                            default => request()->url() === url($child->url) || request()->is(parse_url($child->url, PHP_URL_PATH)),
                                        };
                                    @endphp
                                    <a href="{{ $child->resolved_url }}"
                                        wire:navigate
                                        @if($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                        class="block px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $childIsActive ? 'bg-zinc-100 dark:bg-white/[0.08] text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white' }}">
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                <button
                    @click="searchOpen = !searchOpen; if (searchOpen) mobileMenuOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    :class="scrolled || mobileMenuOpen ? 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08]' : 'text-white hover:bg-white/10'"
                    aria-label="{{ __('Toggle search') }}"
                    :aria-expanded="searchOpen ? 'true' : 'false'"
                >
                    <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"></path>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full text-xs font-bold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white" :class="scrolled ? 'text-zinc-700 dark:text-zinc-200 bg-zinc-100 dark:bg-white/[0.08] hover:bg-zinc-200 dark:hover:bg-white/[0.14]' : 'text-white bg-white/15 hover:bg-white/25'" aria-label="{{ __('My account') }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white" :class="scrolled || mobileMenuOpen ? 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08]' : 'text-white hover:bg-white/10'" aria-label="{{ __('Log in') }}">
                        <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"></path>
                        </svg>
                    </a>
                @endauth

                <button
                    @click="$dispatch('open-cart')"
                    class="relative flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    :class="scrolled || mobileMenuOpen ? 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08]' : 'text-white hover:bg-white/10'"
                    aria-label="{{ __('Shopping Cart') }}"
                >
                    <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"></path>
                    </svg>
                    <span
                        x-show="cartCount > 0"
                        x-text="cartCount > 99 ? '99+' : cartCount"
                        style="display: none"
                        class="absolute top-0.5 right-0.5 flex items-center justify-center min-w-[16px] h-[16px] px-0.5 rounded-full bg-red-500 text-[9px] font-bold text-white tabular-nums ring-2 transition-colors duration-300"
                        :class="scrolled || mobileMenuOpen ? 'ring-white dark:ring-zinc-950' : 'ring-transparent'"
                    ></span>
                </button>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen; if (mobileMenuOpen) searchOpen = false"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    :class="scrolled || mobileMenuOpen ? 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/[0.08]' : 'text-white hover:bg-white/10'"
                    aria-label="{{ __('Toggle menu') }}"
                    :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
                >
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Slide-down search row (always solid, regardless of scroll) --}}
        <div
            x-show="searchOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="pb-4"
            style="display: none;"
        >
            <livewire:nav-search variant="mobile" :key="'nav-search-transparent'" />
        </div>
    </div>

    {{-- Mobile menu panel (always solid) --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        class="lg:hidden border-t border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-950 max-h-[calc(100dvh-4rem)] overflow-y-auto"
        style="display: none;"
    >
        <div class="container mx-auto frontend-container px-6 sm:px-8 py-4 space-y-4">
            @if($categories->count() > 0)
                <div class="flex flex-wrap gap-1.5">
                    @foreach($categories as $category)
                        <a href="/category/{{ $category->slug }}" wire:navigate @click="mobileMenuOpen = false" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-white/[0.08] text-zinc-600 dark:text-zinc-300">{{ $category->name }}</a>
                    @endforeach
                </div>
            @endif

            @include('components.public.navbars._nav-links-mobile')

            <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-white/10">
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('My Account') }}</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Log in') }}</a>
                @endauth
                <div class="flex items-center gap-1 rounded-full bg-zinc-100 dark:bg-white/[0.08] p-1">
                    <a href="{{ route('change-language', 'en') }}" wire:navigate class="px-3 py-1 rounded-full text-xs font-bold transition-colors duration-200 {{ app()->getLocale() === 'en' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900' : 'text-zinc-500 dark:text-zinc-400' }}">{{ __('EN') }}</a>
                    <a href="{{ route('change-language', 'bn') }}" wire:navigate class="px-3 py-1 rounded-full text-xs font-bold transition-colors duration-200 {{ app()->getLocale() === 'bn' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900' : 'text-zinc-500 dark:text-zinc-400' }}">{{ __('BN') }}</a>
                </div>
            </div>
        </div>
    </div>
</header>
