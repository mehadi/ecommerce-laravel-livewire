{{-- Noir footer — always near-black for a premium, high-contrast finish. --}}
<footer class="relative bg-zinc-950 text-white pt-16 sm:pt-20 pb-10 overflow-hidden">
    {{-- Emerald accent line + glow --}}
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-emerald-500/70 to-transparent"></div>
    <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-emerald-500/[0.08] rounded-full blur-3xl"></div>

    <div class="relative container mx-auto px-4 sm:px-6 frontend-container">
        <div class="grid gap-12 md:gap-8 md:grid-cols-2 lg:grid-cols-[1.6fr_1fr_1fr_1.2fr] mb-14">
            <div class="space-y-5">
                <a href="/" wire:navigate class="inline-flex items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950">
                    @if($siteLogo)
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" loading="lazy" class="h-11 w-auto">
                    @else
                        <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md shadow-emerald-500/25">
                            <span class="text-white font-display font-bold text-lg">{{ substr($siteName, 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="font-display text-xl font-semibold tracking-tight">{{ $siteName }}</span>
                </a>
                @if($siteTagline)
                    <p class="text-zinc-400 leading-relaxed text-sm sm:text-[15px] max-w-xs">{{ $siteTagline }}</p>
                @endif
                @if(count($socialLinks) > 0)
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($socialLinks as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-white/[0.05] ring-1 ring-white/10 hover:bg-emerald-500/15 hover:ring-emerald-400/40 hover:text-emerald-300 text-zinc-400 rounded-full flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" aria-label="{{ $link['label'] }}">
                                <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $link['path'] }}"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <nav aria-label="{{ __('Company') }}">
                <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-400/90 mb-5">{{ __('Company') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    @foreach($footerCompanyLinks as $link)
                        <li><a href="{{ $link['url'] }}" wire:navigate class="text-zinc-400 hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-md">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
            @if(count($footerLegalLinks))
                <nav aria-label="{{ __('Legal') }}">
                    <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-400/90 mb-5">{{ __('Legal') }}</h4>
                    <ul class="space-y-3.5 text-[15px]">
                        @foreach($footerLegalLinks as $link)
                            <li><a href="{{ $link['url'] }}" wire:navigate class="text-zinc-400 hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-md">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-400/90 mb-5">{{ __('Contact Us') }}</h4>
                <ul class="space-y-3.5 text-[15px] text-zinc-400">
                    @if($contactEmail)
                        <li>
                            <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-3 hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-md">
                                <svg class="w-[18px] h-[18px] text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="break-all">{{ $contactEmail }}</span>
                            </a>
                        </li>
                    @endif
                    @if($contactPhone)
                        <li>
                            <a href="tel:{{ $contactPhone }}" class="inline-flex items-center gap-3 hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-md">
                                <svg class="w-[18px] h-[18px] text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="tabular-nums">{{ $contactPhone }}</span>
                            </a>
                        </li>
                    @endif
                    @if($contactAddress)
                        <li class="flex items-start gap-3">
                            <svg class="w-[18px] h-[18px] text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $contactAddress }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-zinc-500 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved') }}.
                </p>
                <div class="flex items-center gap-6 text-zinc-500 text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Secure Checkout') }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('SSL Encrypted') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>
