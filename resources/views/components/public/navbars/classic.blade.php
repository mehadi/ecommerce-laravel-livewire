{{-- Floating Pill: the original floating rounded bar, fully driven by the
     per-tenant NavbarComponent drag-and-drop layout at /admin/navigation. --}}
@php
    use App\Models\NavbarComponent;
    use App\Support\Tenancy;
    use Illuminate\Support\Facades\Cache;

    // Get navbar layout (order, grid span, visibility) per zone
    $barComponents = Cache::remember(Tenancy::cacheKey('navbar.components.desktop'), 3600, function () {
        return NavbarComponent::forZone('desktop')->get();
    });
    $menuComponents = Cache::remember(Tenancy::cacheKey('navbar.components.mobile'), 3600, function () {
        return NavbarComponent::forZone('mobile')->get();
    });
@endphp

<nav
    x-data="{ mobileMenuOpen: false, scrolled: false, cartCount: {{ $cartItemCount }} }"
    x-on:cart-updated.window="cartCount = $event.detail.count"
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
            {{-- Admin-configurable navbar layout (order + zone from /admin/navigation): three
                 regions across the bar. Start and End hug their own content (shrink-0, "short"),
                 Middle absorbs whatever space is left (flex-1, "long") so it naturally separates
                 the brand/actions from the main content without per-component width math. --}}
            @php
                $startComponents = $barComponents->where('zone_desktop', 'start');
                $middleComponents = $barComponents->where('zone_desktop', 'middle');
                $endComponents = $barComponents->where('zone_desktop', 'end');
            @endphp
            <div class="flex items-center gap-x-1.5 sm:gap-x-2 flex-1 min-w-0">
                <div class="flex items-center gap-x-1.5 sm:gap-x-2 shrink-0">
                    @foreach($startComponents as $component)
                        <div class="shrink-0">
                            @include('components.public.navbar.desktop.'.str_replace('_', '-', $component->key))
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-x-1.5 sm:gap-x-2 flex-1 min-w-0">
                    @foreach($middleComponents as $component)
                        {{-- Search grows to soak up whatever room Middle has left (capped by
                             its own max-width); other Middle items just sit at natural size. --}}
                        <div class="{{ $component->key === 'search' ? 'flex-1 min-w-0' : 'shrink-0' }}">
                            @include('components.public.navbar.desktop.'.str_replace('_', '-', $component->key))
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-x-1.5 sm:gap-x-2 shrink-0">
                    @foreach($endComponents as $component)
                        <div class="shrink-0">
                            @include('components.public.navbar.desktop.'.str_replace('_', '-', $component->key))
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Mobile menu button --}}
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-zinc-100 dark:bg-white/[0.07] text-zinc-800 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-white/[0.12] transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white"
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
            class="pointer-events-auto lg:hidden mt-2.5 max-h-[calc(100dvh-7rem)] overflow-y-auto rounded-[1.75rem]"
            style="display: none;"
        >
            {{-- Admin-configurable navbar components (order + grid span from /admin/navigation) --}}
            <div class="grid grid-cols-12 gap-2.5">
                @foreach($menuComponents as $component)
                    <div style="grid-column: span {{ $component->span_mobile }} / span {{ $component->span_mobile }};">
                        @include('components.public.navbar.mobile.'.str_replace('_', '-', $component->key))
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</nav>
