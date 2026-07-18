@php
    $siteName = config('app.name');
    $navLinks = [
        ['href' => '#features', 'label' => __('Features')],
        ['href' => '#pricing', 'label' => __('Pricing')],
        ['href' => '#faq', 'label' => __('FAQ')],
    ];
@endphp

<nav
    x-data="{ mobileMenuOpen: false, scrolled: false }"
    x-init="
        window.addEventListener('scroll', () => {
            scrolled = window.scrollY > 20;
        }, { passive: true });
    "
    class="fixed top-0 left-0 right-0 z-50 pointer-events-none"
    aria-label="{{ __('Main navigation') }}"
    @keydown.escape.window="mobileMenuOpen = false"
>
    {{-- Mobile menu backdrop --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="pointer-events-auto lg:hidden fixed inset-0 bg-zinc-950/40 backdrop-blur-[2px]"
        style="display: none;"
        aria-hidden="true"
    ></div>

    <div class="relative container mx-auto frontend-container px-6 sm:px-8 lg:px-10 pt-4 sm:pt-5">
        {{-- Floating white pill bar --}}
        <div
            class="pointer-events-auto relative flex items-center gap-2 sm:gap-3 h-14 sm:h-16 rounded-full bg-white dark:bg-zinc-900 pl-3 pr-2 sm:pl-4 sm:pr-2.5 ring-1 transition-all duration-300 ease-out"
            :class="scrolled || mobileMenuOpen
                ? 'ring-zinc-900/[0.08] dark:ring-white/[0.1] shadow-[0_4px_10px_-2px_rgb(16_24_40_/_0.08),0_20px_48px_-16px_rgb(16_24_40_/_0.22)]'
                : 'ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_2px_6px_-1px_rgb(16_24_40_/_0.05),0_12px_32px_-16px_rgb(16_24_40_/_0.14)]'"
        >
            {{-- Brand --}}
            <a href="{{ route('platform.home') }}" wire:navigate class="flex items-center gap-2.5 shrink-0 pr-2 rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                <span class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center shadow-md shadow-emerald-600/20 shrink-0">
                    <span class="text-white font-display font-bold text-sm sm:text-base">{{ substr($siteName, 0, 1) }}</span>
                </span>
                <span class="hidden sm:block font-display text-base font-semibold tracking-tight text-zinc-900 dark:text-white truncate">{{ $siteName }}</span>
            </a>

            {{-- Desktop links --}}
            <div class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                @foreach($navLinks as $link)
                    <a href="{{ $link['href'] }}" class="px-4 py-2 rounded-full text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-white/[0.06] transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Desktop actions --}}
            <div class="hidden lg:flex items-center gap-2 shrink-0">
                <x-platform.language-switcher class="hidden xl:flex" />

                <button
                    type="button"
                    @click="
                        let dark = !document.documentElement.classList.contains('dark');
                        document.documentElement.classList.toggle('dark', dark);
                        window.localStorage.setItem('flux.appearance', dark ? 'dark' : 'light');
                    "
                    class="flex items-center justify-center w-10 h-10 rounded-full text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.08] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                    aria-label="{{ __('Toggle dark mode') }}"
                >
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"></path>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"></path>
                    </svg>
                </button>

                <a href="{{ route('login') }}" wire:navigate class="px-4 py-2 rounded-full text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/[0.08] transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    {{ __('Login') }}
                </a>
                <a href="{{ route('register') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    {{ __('Get Started') }}
                </a>
            </div>

            {{-- Mobile menu button --}}
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden ml-auto flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-zinc-100 dark:bg-white/[0.07] text-zinc-800 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-white/[0.12] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white"
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

        {{-- Mobile Menu --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="pointer-events-auto lg:hidden mt-2.5 rounded-[1.75rem] bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_4px_10px_-2px_rgb(16_24_40_/_0.08),0_20px_48px_-16px_rgb(16_24_40_/_0.22)] p-4"
            style="display: none;"
        >
            <div class="flex flex-col gap-1">
                @foreach($navLinks as $link)
                    <a href="{{ $link['href'] }}" @click="mobileMenuOpen = false" class="px-4 py-3 rounded-2xl text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-white/[0.06] transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <div class="flex items-center gap-2 mt-1">
                    <x-platform.language-switcher class="flex flex-1" />

                    <button
                        type="button"
                        @click="
                        let dark = !document.documentElement.classList.contains('dark');
                        document.documentElement.classList.toggle('dark', dark);
                        window.localStorage.setItem('flux.appearance', dark ? 'dark' : 'light');
                    "
                        class="flex items-center justify-center w-11 h-11 shrink-0 rounded-full bg-zinc-50 dark:bg-white/[0.06] text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/[0.1] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                        aria-label="{{ __('Toggle dark mode') }}"
                    >
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"></path>
                        </svg>
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"></path>
                        </svg>
                    </button>
                </div>

                <div class="h-px bg-zinc-900/[0.06] dark:bg-white/[0.08] my-2"></div>
                <a href="{{ route('login') }}" wire:navigate class="px-4 py-3 rounded-2xl text-sm font-semibold text-center text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-white/[0.06] transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    {{ __('Login') }}
                </a>
                <a href="{{ route('register') }}" wire:navigate class="px-4 py-3 rounded-2xl text-sm font-bold text-center bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    {{ __('Get Started') }}
                </a>
            </div>
        </div>
    </div>
</nav>
