{{-- Minimal Line: quiet full-width bar, thin bottom border, text-only links,
     no shadows or pills. Search opens as a slim slide-down row. --}}
<header
    x-data="{ mobileMenuOpen: false, searchOpen: false, cartCount: {{ $cartItemCount }} }"
    x-on:cart-updated.window="cartCount = $event.detail.count"
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-sm border-b border-zinc-200/80 dark:border-white/10"
    @keydown.escape.window="mobileMenuOpen = false; searchOpen = false"
>
    <div class="container mx-auto frontend-container px-6 sm:px-8 lg:px-10">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
            <a href="/" wire:navigate class="flex items-center gap-2.5 shrink-0 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white rounded-md">
                @if($siteLogo)
                    <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" class="h-8 sm:h-9 w-auto">
                @else
                    <span class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-md bg-zinc-900 dark:bg-white">
                        <span class="text-white dark:text-zinc-900 font-display font-extrabold text-base sm:text-lg leading-none">{{ substr($siteName, 0, 1) }}</span>
                    </span>
                @endif
                <span class="font-display text-base sm:text-lg font-bold tracking-tight text-zinc-900 dark:text-white whitespace-nowrap">{{ $siteName }}</span>
            </a>

            @include('components.public.navbars._nav-links')

            <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                <button
                    @click="searchOpen = !searchOpen; if (searchOpen) mobileMenuOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-full text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white"
                    aria-label="{{ __('Toggle search') }}"
                    :aria-expanded="searchOpen ? 'true' : 'false'"
                >
                    <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"></path>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full text-xs font-bold text-zinc-700 dark:text-zinc-200 bg-zinc-100 dark:bg-white/[0.08] hover:bg-zinc-200 dark:hover:bg-white/[0.14] transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white" aria-label="{{ __('My account') }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08] transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white" aria-label="{{ __('Log in') }}">
                        <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"></path>
                        </svg>
                    </a>
                @endauth

                <button
                    @click="$dispatch('open-cart')"
                    class="relative flex items-center justify-center w-10 h-10 rounded-full text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white"
                    aria-label="{{ __('Shopping Cart') }}"
                >
                    <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"></path>
                    </svg>
                    <span
                        x-show="cartCount > 0"
                        x-text="cartCount > 99 ? '99+' : cartCount"
                        style="display: none"
                        class="absolute top-0.5 right-0.5 flex items-center justify-center min-w-[16px] h-[16px] px-0.5 rounded-full bg-zinc-900 dark:bg-white text-[9px] font-bold text-white dark:text-zinc-900 tabular-nums"
                    ></span>
                </button>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen; if (mobileMenuOpen) searchOpen = false"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-full text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/[0.08] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white"
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

        {{-- Slide-down search row --}}
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
            <livewire:nav-search variant="mobile" :key="'nav-search-minimal'" />
        </div>
    </div>

    {{-- Mobile menu panel --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        class="lg:hidden border-t border-zinc-200/80 dark:border-white/10 bg-white dark:bg-zinc-950 max-h-[calc(100dvh-4rem)] overflow-y-auto"
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
