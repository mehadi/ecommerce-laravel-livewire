{{-- Editorial footer — oversized brand wordmark, hairline dividers and airy link rows. --}}
<footer class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-t border-zinc-900/[0.08] dark:border-white/[0.1] pt-14 sm:pt-16 pb-8">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        {{-- Top row: tagline + links + contact --}}
        <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr] pb-12">
            <div class="space-y-4">
                @if($siteTagline)
                    <p class="font-display text-xl sm:text-2xl leading-snug tracking-tight max-w-sm">{{ $siteTagline }}</p>
                @else
                    <p class="font-display text-xl sm:text-2xl leading-snug tracking-tight max-w-sm">{{ $siteName }}</p>
                @endif
                @if(count($socialLinks) > 0)
                    <div class="flex flex-wrap gap-x-5 gap-y-2 pt-2">
                        @foreach($socialLinks as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm uppercase tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $link['path'] }}"/>
                                </svg>
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <nav aria-label="{{ __('Company') }}" class="md:justify-self-center">
                <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-zinc-400 dark:text-zinc-500 mb-5">{{ __('Company') }}</h4>
                <ul class="space-y-3 text-[15px]">
                    @foreach(array_merge($footerCompanyLinks, $footerLegalLinks) as $link)
                        <li><a href="{{ $link['url'] }}" class="text-zinc-600 dark:text-zinc-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <div class="md:justify-self-end">
                <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-zinc-400 dark:text-zinc-500 mb-5">{{ __('Contact Us') }}</h4>
                <ul class="space-y-3 text-[15px] text-zinc-600 dark:text-zinc-300">
                    @if($contactEmail)
                        <li><a href="mailto:{{ $contactEmail }}" class="break-all underline decoration-zinc-300 dark:decoration-zinc-600 decoration-1 underline-offset-4 hover:decoration-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ $contactEmail }}</a></li>
                    @endif
                    @if($contactPhone)
                        <li><a href="tel:{{ $contactPhone }}" class="tabular-nums underline decoration-zinc-300 dark:decoration-zinc-600 decoration-1 underline-offset-4 hover:decoration-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ $contactPhone }}</a></li>
                    @endif
                    @if($contactAddress)
                        <li class="text-zinc-500 dark:text-zinc-400">{{ $contactAddress }}</li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Oversized wordmark --}}
        <div class="border-t border-zinc-900/[0.08] dark:border-white/[0.1] py-8 sm:py-10 overflow-hidden">
            <a href="/" wire:navigate class="block rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ $siteName }}">
                <span class="block font-display font-bold tracking-tighter leading-none text-[clamp(2.5rem,11vw,9rem)] text-zinc-900 dark:text-white whitespace-nowrap" aria-hidden="true">{{ $siteName }}</span>
            </a>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-zinc-900/[0.08] dark:border-white/[0.1] pt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved') }}.
            </p>
            <div class="flex items-center gap-5 text-xs uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                <span>{{ __('Secure Checkout') }}</span>
                <span aria-hidden="true" class="w-1 h-1 rounded-full bg-zinc-300 dark:bg-zinc-600"></span>
                <span>{{ __('SSL Encrypted') }}</span>
            </div>
        </div>
    </div>
</footer>
