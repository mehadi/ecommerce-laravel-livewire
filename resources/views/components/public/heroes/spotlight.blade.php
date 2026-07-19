{{-- Dark Spotlight: near-black stage with a glowing spotlight on the centered product. --}}
<section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="relative overflow-hidden rounded-[2rem] bg-zinc-950 ring-1 ring-white/[0.06] shadow-[0_16px_40px_-20px_rgb(0_0_0_/_0.5)] px-6 sm:px-10 pb-10 sm:pb-12 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[7rem]">
            {{-- Spotlight beam --}}
            <div class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[640px] h-[640px] rounded-full bg-[radial-gradient(circle_at_center,_var(--tenant-primary)_0%,_transparent_60%)] opacity-25" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/[0.04] to-transparent" aria-hidden="true"></div>

            <div class="relative mx-auto max-w-3xl text-center flex flex-col items-center gap-5">
                @if($heroBadge)
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.06] backdrop-blur text-xs font-semibold text-white/80 ring-1 ring-white/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--tenant-primary)] shadow-[0_0_8px_var(--tenant-primary)]" aria-hidden="true"></span>
                        {{ $heroBadge }}
                    </span>
                @endif

                <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.5rem] font-bold text-white leading-[1.05] tracking-tight text-balance break-words">
                    {{ $heroTitle }}
                </h1>

                @if($heroContent)
                    <p class="text-base sm:text-lg text-white/60 leading-relaxed max-w-2xl">{{ $heroContent }}</p>
                @endif
            </div>

            {{-- Product under the spotlight --}}
            @if($heroImage)
                <div class="relative mt-6 sm:mt-8 flex items-center justify-center min-h-[240px] sm:min-h-[300px]">
                    <span class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-[var(--tenant-primary)]/20 blur-3xl" aria-hidden="true"></span>
                    <span class="absolute bottom-2 left-1/2 -translate-x-1/2 w-48 sm:w-64 h-5 rounded-[100%] bg-[var(--tenant-primary)]/25 blur-md" aria-hidden="true"></span>
                    <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="480" fetchpriority="high" class="relative max-h-56 sm:max-h-72 w-auto object-contain drop-shadow-[0_25px_35px_rgb(0_0_0_/_0.6)] transition-transform duration-500 hover:scale-[1.04] motion-reduce:transform-none">
                </div>
            @endif

            {{-- CTA --}}
            <div class="relative mt-6 sm:mt-8 flex flex-wrap items-center justify-center gap-3">
                @if($product)
                    <a href="{{ $heroPrimaryCtaUrl }}" @if($heroPrimaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-8 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg shadow-black/40 transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950">
                        {{ $heroPrimaryCtaLabel }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                    </a>
                @endif
                <a href="{{ $heroSecondaryCtaUrl }}" @if($heroSecondaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-bold text-sm sm:text-base text-white/90 bg-white/[0.06] ring-1 ring-white/10 hover:bg-white/[0.12] transition-colors backdrop-blur focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                    {{ $heroSecondaryCtaLabel }}
                </a>
            </div>

            {{-- Stats strip --}}
            @if($heroShowStats)
            <div class="relative mt-8 sm:mt-10 mx-auto max-w-2xl flex items-center justify-center gap-6 sm:gap-10 pt-6 border-t border-white/[0.08] text-center">
                <div>
                    <p class="font-display text-xl sm:text-2xl font-bold text-white tabular-nums">{{ $heroOrderCountLabel }}</p>
                    <p class="text-xs text-white/50">{{ __('Orders Delivered') }}</p>
                </div>
                <div class="w-px h-10 bg-white/[0.1]" aria-hidden="true"></div>
                <div>
                    <p class="font-display text-xl sm:text-2xl font-bold text-white tabular-nums">{{ $heroExtras['productCount'] }}+</p>
                    <p class="text-xs text-white/50">{{ __('Products') }}</p>
                </div>
                @if($heroExtras['testimonialCount'] > 0)
                    <div class="w-px h-10 bg-white/[0.1]" aria-hidden="true"></div>
                    <div>
                        <p class="font-display text-xl sm:text-2xl font-bold text-white tabular-nums flex items-center justify-center gap-1">
                            {{ $heroExtras['avgRating'] }}
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </p>
                        <p class="text-xs text-white/50">{{ __('reviews') }}</p>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
