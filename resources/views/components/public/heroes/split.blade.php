{{-- Split Screen: edge-to-edge 50/50 — solid brand panel left, full-bleed image right. --}}
<section class="relative grid lg:grid-cols-2 min-h-[76vh]">
    {{-- Brand panel --}}
    <div class="relative overflow-hidden bg-[var(--tenant-secondary)] flex">
        <div class="pointer-events-none absolute -bottom-24 -left-24 w-80 h-80 bg-[var(--tenant-primary)]/20 rounded-full blur-3xl" aria-hidden="true"></div>

        <div class="relative w-full max-w-2xl ml-auto px-6 sm:px-10 lg:px-16 pt-32 sm:pt-36 pb-14 sm:pb-16 flex flex-col justify-center gap-5 sm:gap-6">
            @if($heroBadge)
                <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full text-xs font-semibold text-white/90 ring-1 ring-white/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--tenant-primary)]" aria-hidden="true"></span>
                    {{ $heroBadge }}
                </span>
            @endif

            <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.4rem] font-bold text-white leading-[1.05] tracking-tight text-balance break-words">
                {{ $heroTitle }}
            </h1>

            @if($heroContent)
                <p class="text-base sm:text-lg text-white/70 leading-relaxed">{{ $heroContent }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                @if($product)
                    <a href="#product" class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-7 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg shadow-black/25 transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                        {{ __('Order Now') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                    </a>
                @endif
                <a href="/shop" wire:navigate class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-bold text-sm sm:text-base text-white/90 ring-1 ring-white/25 hover:bg-white/10 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                    {{ __('Browse Shop') }}
                </a>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-6 sm:gap-8 pt-4 border-t border-white/10">
                <div>
                    <p class="font-display text-xl sm:text-2xl font-bold text-white tabular-nums">{{ $heroOrderCountLabel }}</p>
                    <p class="text-xs text-white/60">{{ __('Orders Delivered') }}</p>
                </div>
                <div class="w-px h-10 bg-white/15" aria-hidden="true"></div>
                <div>
                    <p class="font-display text-xl sm:text-2xl font-bold text-white tabular-nums">{{ $heroExtras['productCount'] }}+</p>
                    <p class="text-xs text-white/60">{{ __('Products') }}</p>
                </div>
                @if($heroExtras['testimonialCount'] > 0)
                    <div class="w-px h-10 bg-white/15" aria-hidden="true"></div>
                    <div>
                        <p class="font-display text-xl sm:text-2xl font-bold text-white tabular-nums flex items-center gap-1">
                            {{ $heroExtras['avgRating'] }}
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </p>
                        <p class="text-xs text-white/60">{{ __('reviews') }}</p>
                    </div>
                @endif
            </div>

            @include('components.public.heroes._social', ['tone' => 'dark'])
        </div>
    </div>

    {{-- Image panel --}}
    <div class="relative overflow-hidden min-h-[320px] lg:min-h-0 bg-zinc-100 dark:bg-zinc-900">
        @if($heroImage)
            <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="960" height="960" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent lg:bg-gradient-to-r lg:from-black/20 lg:to-transparent" aria-hidden="true"></div>
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="absolute w-72 h-72 rounded-full bg-[var(--tenant-primary)]/15 blur-3xl" aria-hidden="true"></span>
                <p class="relative font-display text-6xl font-bold text-zinc-300 dark:text-zinc-700 tabular-nums">{{ $heroExtras['productCount'] }}+</p>
            </div>
        @endif
        @if($product)
            <div class="absolute bottom-6 left-6 bg-white/90 dark:bg-zinc-900/90 backdrop-blur rounded-2xl px-4 py-2.5 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08]">
                <p class="text-sm font-bold text-zinc-900 dark:text-white line-clamp-1">{{ $product->name }}</p>
            </div>
        @endif
    </div>
</section>
