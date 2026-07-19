{{-- Mega CTA footer — a bold call-to-action panel on top, link columns and a trust strip below. --}}
<footer class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-white pt-16 sm:pt-20 pb-10">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        {{-- CTA panel --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-600 to-green-700 px-6 py-12 sm:px-12 sm:py-14 mb-14 shadow-xl shadow-emerald-600/20">
            <div class="pointer-events-none absolute -top-20 -right-16 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-10 w-72 h-72 bg-emerald-300/20 rounded-full blur-3xl"></div>
            <div class="relative flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                <div class="max-w-xl">
                    <h3 class="font-display text-2xl sm:text-3xl font-bold text-white tracking-tight">{{ __('Ready to place your order?') }}</h3>
                    <p class="mt-2 text-emerald-50/90 text-sm sm:text-base leading-relaxed">{{ $siteTagline ?: __('Browse our full catalog and get your favorites delivered to your door.') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="/shop" wire:navigate class="inline-flex items-center gap-2 rounded-full bg-white text-emerald-700 font-semibold px-7 py-3.5 text-sm sm:text-base shadow-lg shadow-emerald-900/20 hover:bg-emerald-50 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-emerald-600">
                        {{ __('Shop Now') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"></path>
                        </svg>
                    </a>
                    @if($contactPhone)
                        <a href="tel:{{ $contactPhone }}" class="inline-flex items-center gap-2 rounded-full ring-1 ring-white/40 text-white font-semibold px-7 py-3.5 text-sm sm:text-base hover:bg-white/10 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="tabular-nums">{{ $contactPhone }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Link columns --}}
        <div class="grid gap-12 md:gap-8 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr] mb-14">
            <div class="space-y-5">
                <a href="/" wire:navigate class="inline-flex items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-50 dark:focus-visible:ring-offset-zinc-950">
                    @if($siteLogo)
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" loading="lazy" class="h-11 w-auto">
                    @else
                        <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md shadow-emerald-600/20">
                            <span class="text-white font-display font-bold text-lg">{{ substr($siteName, 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="font-display text-xl font-semibold tracking-tight">{{ $siteName }}</span>
                </a>
                @if($siteTagline)
                    <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed text-sm sm:text-[15px] max-w-xs">{{ $siteTagline }}</p>
                @endif
                @if(count($socialLinks) > 0)
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($socialLinks as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-white dark:bg-white/[0.06] ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:bg-emerald-50 dark:hover:bg-emerald-500/15 hover:ring-emerald-500/40 hover:text-emerald-600 dark:hover:text-emerald-400 text-zinc-500 dark:text-zinc-400 rounded-full flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ $link['label'] }}">
                                <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $link['path'] }}"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <nav aria-label="{{ __('Company') }}">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Company') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    @foreach($footerCompanyLinks as $link)
                        <li><a href="{{ $link['url'] }}" class="text-zinc-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <nav aria-label="{{ __('Legal') }}">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Legal') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    @foreach($footerLegalLinks as $link)
                        <li><a href="{{ $link['url'] }}" class="text-zinc-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Contact Us') }}</h4>
                <ul class="space-y-3.5 text-[15px] text-zinc-500 dark:text-zinc-400">
                    @if($contactEmail)
                        <li>
                            <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-3 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                                <svg class="w-[18px] h-[18px] text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="break-all">{{ $contactEmail }}</span>
                            </a>
                        </li>
                    @endif
                    @if($contactPhone)
                        <li>
                            <a href="tel:{{ $contactPhone }}" class="inline-flex items-center gap-3 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                                <svg class="w-[18px] h-[18px] text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="tabular-nums">{{ $contactPhone }}</span>
                            </a>
                        </li>
                    @endif
                    @if($contactAddress)
                        <li class="flex items-start gap-3">
                            <svg class="w-[18px] h-[18px] text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $contactAddress }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Trust strip + copyright --}}
        <div class="border-t border-zinc-900/[0.06] dark:border-white/[0.08] pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-zinc-500 dark:text-zinc-400 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved') }}.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-zinc-500 dark:text-zinc-400 text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Secure Checkout') }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('SSL Encrypted') }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path>
                        </svg>
                        {{ __('Fast Delivery') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>
