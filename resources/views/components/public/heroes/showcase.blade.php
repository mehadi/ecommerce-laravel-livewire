{{-- Product Showcase: product on a center pedestal with floating stat chips. --}}
<section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="relative rounded-[2rem] bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] px-6 sm:px-10 pb-10 sm:pb-14 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[7rem]">
            <div class="mx-auto max-w-3xl text-center flex flex-col items-center gap-5">
                @if($heroBadge)
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-50 dark:bg-zinc-800 text-xs font-semibold text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--tenant-primary)]" aria-hidden="true"></span>
                        {{ $heroBadge }}
                    </span>
                @endif

                <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.5rem] font-bold text-zinc-900 dark:text-white leading-[1.05] tracking-tight text-balance break-words">
                    {{ $heroTitle }}
                </h1>

                @if($heroContent)
                    <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl">{{ $heroContent }}</p>
                @endif
            </div>

            {{-- Pedestal stage --}}
            @if($heroImage)
                <div class="relative mt-8 sm:mt-10 mx-auto max-w-2xl flex items-end justify-center min-h-[280px] sm:min-h-[340px]">
                    {{-- Pedestal circle --}}
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-64 h-64 sm:w-80 sm:h-80 rounded-full bg-gradient-to-b from-zinc-50 to-zinc-100 dark:from-zinc-800/80 dark:to-zinc-800/40 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]" aria-hidden="true"></span>
                    <span class="absolute bottom-6 left-1/2 -translate-x-1/2 w-44 sm:w-56 h-6 rounded-[100%] bg-zinc-900/10 dark:bg-black/40 blur-md" aria-hidden="true"></span>

                    <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="480" fetchpriority="high" class="relative max-h-64 sm:max-h-80 w-auto object-contain drop-shadow-2xl transition-transform duration-500 hover:scale-[1.04] motion-reduce:transform-none">

                    {{-- Floating stat chips --}}
                    @if($heroShowStats)
                    <div class="hidden sm:flex absolute top-8 -left-4 lg:-left-16 items-center gap-2.5 bg-white dark:bg-zinc-800 rounded-2xl px-4 py-3 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] animate-float motion-reduce:animate-none">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-white tabular-nums leading-tight">{{ $heroOrderCountLabel }}</p>
                            <p class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ __('Orders Delivered') }}</p>
                        </div>
                    </div>
                    @if($heroExtras['testimonialCount'] > 0)
                        <div class="hidden sm:flex absolute top-20 -right-4 lg:-right-16 items-center gap-2 bg-white dark:bg-zinc-800 rounded-2xl px-4 py-3 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] animate-float-delayed motion-reduce:animate-none">
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <div>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white tabular-nums leading-tight">{{ $heroExtras['avgRating'] }}</p>
                                <p class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ __('reviews') }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="hidden lg:flex absolute bottom-10 -left-8 items-center gap-2.5 bg-white dark:bg-zinc-800 rounded-2xl px-4 py-3 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08]">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-white tabular-nums leading-tight">{{ $heroExtras['productCount'] }}+</p>
                            <p class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ __('Products') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            @endif

            {{-- CTA row --}}
            <div class="mt-8 sm:mt-10 flex flex-wrap items-center justify-center gap-3">
                @if($product)
                    <a href="{{ $heroPrimaryCtaUrl }}" @if($heroPrimaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-8 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)] focus-visible:ring-offset-2">
                        {{ $heroPrimaryCtaLabel }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                    </a>
                @endif
                <a href="{{ $heroSecondaryCtaUrl }}" @if($heroSecondaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-bold text-sm sm:text-base text-zinc-800 dark:text-zinc-100 bg-zinc-50 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white">
                    {{ $heroSecondaryCtaLabel }}
                </a>
            </div>
        </div>
    </div>
</section>
