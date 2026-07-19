{{-- Editorial: magazine-style oversized headline with thin rules and a framed image. --}}
<section class="relative overflow-hidden pt-24 sm:pt-28 pb-12 sm:pb-16">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        {{-- Meta row --}}
        <div class="flex items-center gap-4 pb-5 border-b border-zinc-900/[0.1] dark:border-white/[0.12]">
            @if($heroBadge)
                <span class="text-[11px] sm:text-xs font-bold uppercase tracking-[0.18em] text-zinc-900 dark:text-white shrink-0">{{ $heroBadge }}</span>
            @endif
            <span class="flex-1 h-px bg-zinc-900/[0.08] dark:bg-white/[0.1]" aria-hidden="true"></span>
            @if($heroExtras['testimonialCount'] > 0)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 tabular-nums shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    {{ $heroExtras['avgRating'] }} · {{ $heroExtras['testimonialCount'] }} {{ __('reviews') }}
                </span>
            @endif
        </div>

        {{-- Oversized headline --}}
        <h1 class="font-display text-[2.75rem] sm:text-6xl lg:text-[5.25rem] font-bold text-zinc-900 dark:text-white leading-[0.98] tracking-tight text-balance break-words py-6 sm:py-10">
            {{ $heroTitle }}
        </h1>

        <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 pt-6 border-t border-zinc-900/[0.1] dark:border-white/[0.12]">
            {{-- Content + CTA --}}
            <div class="lg:col-span-5 flex flex-col gap-6">
                @if($heroContent)
                    <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        {{ $heroContent }}
                    </p>
                @endif

                @if($product)
                    <p class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ __('Featuring') }} — <span class="text-zinc-900 dark:text-white">{{ $product->name }}</span></p>
                @endif

                <div class="flex flex-wrap items-center gap-5">
                    @if($product)
                        <a href="{{ $heroPrimaryCtaUrl }}" @if($heroPrimaryCtaNavigate) wire:navigate @endif class="group inline-flex items-center gap-3 font-bold text-sm sm:text-base text-zinc-900 dark:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white rounded">
                            <span class="border-b-2 border-[var(--tenant-primary)] pb-0.5">{{ $heroPrimaryCtaLabel }}</span>
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 transition-transform duration-300 group-hover:rotate-45 motion-reduce:transform-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9"></path></svg>
                            </span>
                        </a>
                    @endif
                    <a href="{{ $heroSecondaryCtaUrl }}" @if($heroSecondaryCtaNavigate) wire:navigate @endif class="text-sm sm:text-base font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors underline underline-offset-4 decoration-zinc-300 dark:decoration-zinc-600">
                        {{ $heroSecondaryCtaLabel }}
                    </a>
                </div>

                @if($heroShowStats)
                <div class="mt-auto pt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">
                    <span><span class="font-bold text-zinc-900 dark:text-white tabular-nums normal-case">{{ $heroOrderCountLabel }}</span> {{ __('Orders Delivered') }}</span>
                    <span><span class="font-bold text-zinc-900 dark:text-white tabular-nums normal-case">{{ $heroExtras['productCount'] }}+</span> {{ __('Products') }}</span>
                </div>
                @endif

                @include('components.public.heroes._social')
            </div>

            {{-- Framed image --}}
            @if($heroImage)
                <div class="lg:col-span-7">
                    <figure class="relative">
                        <div class="rounded-2xl overflow-hidden bg-zinc-200 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08]">
                            <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="960" height="640" fetchpriority="high" class="w-full max-h-[440px] object-cover">
                        </div>
                        @if($product)
                            <figcaption class="mt-3 text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-[0.15em]">№ 01 — {{ $product->name }}</figcaption>
                        @endif
                    </figure>
                </div>
            @endif
        </div>
    </div>
</section>
