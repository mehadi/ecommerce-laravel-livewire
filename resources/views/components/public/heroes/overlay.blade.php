{{-- Image Overlay: full-width hero photo with dark overlay, copy anchored bottom-left. --}}
<section class="relative overflow-hidden min-h-[70vh] sm:min-h-[76vh] flex items-end">
    {{-- Background image / fallback gradient --}}
    @if($heroImage)
        <img src="{{ asset('storage/'.$heroImage) }}" alt="" width="1600" height="900" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[var(--tenant-secondary)] via-zinc-900 to-zinc-950" aria-hidden="true"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-black/20" aria-hidden="true"></div>

    <div class="relative w-full container mx-auto px-4 sm:px-6 frontend-container pt-36 sm:pt-44 pb-12 sm:pb-16">
        <div class="max-w-2xl flex flex-col gap-5 sm:gap-6">
            @if($heroBadge)
                <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur text-xs font-semibold text-white/90 ring-1 ring-white/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--tenant-primary)]" aria-hidden="true"></span>
                    {{ $heroBadge }}
                </span>
            @endif

            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.05] tracking-tight text-balance break-words drop-shadow-lg">
                {{ $heroTitle }}
            </h1>

            @if($heroContent)
                <p class="text-base sm:text-lg text-white/80 leading-relaxed max-w-xl">{{ $heroContent }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                @if($product)
                    <a href="{{ $heroPrimaryCtaUrl }}" @if($heroPrimaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-7 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg shadow-black/30 transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                        {{ $heroPrimaryCtaLabel }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                    </a>
                @endif
                <a href="{{ $heroSecondaryCtaUrl }}" @if($heroSecondaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-bold text-sm sm:text-base text-white bg-white/10 ring-1 ring-white/25 hover:bg-white/20 transition-colors backdrop-blur focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                    {{ $heroSecondaryCtaLabel }}
                </a>
            </div>

            {{-- Stats strip --}}
            @if($heroShowStats)
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 pt-3 text-sm text-white/70">
                <span><span class="font-bold text-white tabular-nums">{{ $heroOrderCountLabel }}</span> {{ __('Orders Delivered') }}</span>
                <span class="w-1 h-1 rounded-full bg-white/40" aria-hidden="true"></span>
                <span><span class="font-bold text-white tabular-nums">{{ $heroExtras['productCount'] }}+</span> {{ __('Products') }}</span>
                @if($heroExtras['testimonialCount'] > 0)
                    <span class="w-1 h-1 rounded-full bg-white/40" aria-hidden="true"></span>
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="font-bold text-white tabular-nums">{{ $heroExtras['avgRating'] }}</span> {{ __('reviews') }}
                    </span>
                @endif
            </div>
            @endif

            @include('components.public.heroes._social', ['tone' => 'dark'])
        </div>
    </div>
</section>
