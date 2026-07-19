{{-- Centered Minimal footer — a single centered column: logo, tagline, inline links, socials. --}}
<footer class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-t border-zinc-900/[0.04] dark:border-white/[0.06] pt-16 sm:pt-20 pb-10">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="max-w-2xl mx-auto flex flex-col items-center text-center gap-6">
            <a href="/" wire:navigate class="inline-flex flex-col items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900">
                @if($siteLogo)
                    <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" loading="lazy" class="h-12 w-auto">
                @else
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md shadow-emerald-600/20">
                        <span class="text-white font-display font-bold text-xl">{{ substr($siteName, 0, 1) }}</span>
                    </div>
                @endif
                <span class="font-display text-2xl font-semibold tracking-tight">{{ $siteName }}</span>
            </a>

            @if($siteTagline)
                <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed text-sm sm:text-[15px] max-w-md">{{ $siteTagline }}</p>
            @endif

            <nav aria-label="{{ __('Footer') }}">
                <ul class="flex flex-wrap items-center justify-center gap-x-7 gap-y-3 text-[15px]">
                    @foreach(array_merge($footerCompanyLinks, $footerLegalLinks) as $link)
                        <li><a href="{{ $link['url'] }}" class="text-zinc-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            @if(count($socialLinks) > 0)
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach($socialLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-zinc-50 dark:bg-white/[0.06] ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:bg-emerald-50 dark:hover:bg-emerald-500/15 hover:ring-emerald-500/40 hover:text-emerald-600 dark:hover:text-emerald-400 text-zinc-500 dark:text-zinc-400 rounded-full flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ $link['label'] }}">
                            <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="{{ $link['path'] }}"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif

            @if($contactEmail || $contactPhone)
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                    @if($contactEmail)
                        <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-2 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="break-all">{{ $contactEmail }}</span>
                        </a>
                    @endif
                    @if($contactPhone)
                        <a href="tel:{{ $contactPhone }}" class="inline-flex items-center gap-2 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="tabular-nums">{{ $contactPhone }}</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div class="border-t border-zinc-900/[0.06] dark:border-white/[0.08] mt-12 pt-8">
            <p class="text-zinc-500 dark:text-zinc-400 text-sm text-center">
                &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved') }}.
            </p>
        </div>
    </div>
</footer>
