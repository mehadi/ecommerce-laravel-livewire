{{-- Classic Split: copy left, product image right, stats row beneath. --}}
<section class="relative overflow-hidden pt-24 sm:pt-28 pb-12 sm:pb-16">
    <div class="pointer-events-none absolute -top-24 right-0 w-96 h-96 bg-[var(--tenant-primary)]/10 rounded-full blur-3xl" aria-hidden="true"></div>

    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            {{-- Copy column --}}
            <div class="flex flex-col gap-5 sm:gap-6">
                @if($heroBadge)
                    <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--tenant-primary)]" aria-hidden="true"></span>
                        {{ $heroBadge }}
                    </span>
                @endif

                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-zinc-900 dark:text-white leading-[1.05] tracking-tight text-balance break-words">
                    {{ $heroTitle }}
                </h1>

                @if($heroContent)
                    <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-xl">{{ $heroContent }}</p>
                @endif

                <div class="flex flex-wrap items-center gap-3">
                    @if($product)
                        <a href="{{ $heroPrimaryCtaUrl }}" @if($heroPrimaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-7 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)] focus-visible:ring-offset-2">
                            {{ $heroPrimaryCtaLabel }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                        </a>
                    @endif
                    <a href="{{ $heroSecondaryCtaUrl }}" @if($heroSecondaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-bold text-sm sm:text-base text-zinc-800 dark:text-zinc-100 bg-white dark:bg-zinc-800 ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white">
                        {{ $heroSecondaryCtaLabel }}
                    </a>
                </div>

                {{-- Stats row --}}
                @if($heroShowStats)
                <div class="flex items-center gap-6 sm:gap-8 pt-4 border-t border-zinc-900/[0.06] dark:border-white/[0.08] mt-2">
                    <div>
                        <p class="font-display text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ $heroOrderCountLabel }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Orders Delivered') }}</p>
                    </div>
                    <div class="w-px h-10 bg-zinc-900/[0.08] dark:bg-white/[0.1]" aria-hidden="true"></div>
                    <div>
                        <p class="font-display text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ $heroExtras['productCount'] }}+</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Products') }}</p>
                    </div>
                    @if($heroExtras['testimonialCount'] > 0)
                        <div class="w-px h-10 bg-zinc-900/[0.08] dark:bg-white/[0.1]" aria-hidden="true"></div>
                        <div>
                            <p class="font-display text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white tabular-nums flex items-center gap-1">
                                {{ $heroExtras['avgRating'] }}
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('reviews') }}</p>
                        </div>
                    @endif
                </div>
                @endif

                @include('components.public.heroes._social')
            </div>

            {{-- Image column --}}
            @if($heroImage)
                <div class="relative">
                    <div class="relative rounded-[2rem] bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_12px_32px_-16px_rgb(16_24_40_/_0.12)] p-8 sm:p-12 flex items-center justify-center min-h-[300px] sm:min-h-[420px]">
                        <div class="pointer-events-none absolute inset-6 rounded-3xl border border-dashed border-zinc-900/[0.06] dark:border-white/[0.08]" aria-hidden="true"></div>
                        <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="480" fetchpriority="high" class="relative max-h-64 sm:max-h-96 w-auto object-contain drop-shadow-2xl transition-transform duration-500 hover:scale-[1.03] motion-reduce:transform-none">
                    </div>
                    @if($product)
                        <div class="absolute -bottom-4 left-8 bg-white dark:bg-zinc-800 rounded-2xl px-4 py-2.5 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08]">
                            <p class="text-sm font-bold text-zinc-900 dark:text-white line-clamp-1">{{ $product->name }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
