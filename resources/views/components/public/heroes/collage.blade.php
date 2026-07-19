{{-- Collage Cards: copy left, tilted photo-card stack right with a floating review chip. --}}
<section class="relative overflow-hidden pt-24 sm:pt-28 pb-12 sm:pb-16">
    <div class="pointer-events-none absolute -top-20 right-10 w-72 h-72 bg-[var(--tenant-primary)]/10 rounded-full blur-3xl" aria-hidden="true"></div>

    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-10 items-center">
            {{-- Copy column --}}
            <div class="flex flex-col gap-5 sm:gap-6">
                @if($heroBadge)
                    <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-sm -rotate-1">
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

                @if($heroShowStats)
                    <p class="text-sm text-zinc-500 dark:text-zinc-400"><span class="font-bold text-zinc-900 dark:text-white tabular-nums">{{ $heroOrderCountLabel }}</span> {{ __('Orders Delivered') }} · <span class="font-bold text-zinc-900 dark:text-white tabular-nums">{{ $heroExtras['productCount'] }}+</span> {{ __('Products') }}</p>
                @endif

                @include('components.public.heroes._social')
            </div>

            {{-- Collage column --}}
            @if($heroImage)
                <div class="relative h-[380px] sm:h-[460px] max-w-lg mx-auto w-full">
                    {{-- Back cards from recent products --}}
                    @if($heroExtras['recentProducts']->count() > 0)
                        <div class="absolute top-0 left-0 w-36 sm:w-44 aspect-[4/5] rounded-3xl overflow-hidden bg-white dark:bg-zinc-800 p-2 shadow-xl ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] -rotate-6">
                            <img src="{{ asset('storage/'.$heroExtras['recentProducts'][0]->primary_image) }}" alt="{{ $heroExtras['recentProducts'][0]->name }}" loading="lazy" class="w-full h-full object-cover rounded-2xl">
                        </div>
                    @endif
                    @if($heroExtras['recentProducts']->count() > 1)
                        <div class="absolute bottom-2 right-0 w-40 sm:w-48 aspect-[4/5] rounded-3xl overflow-hidden bg-white dark:bg-zinc-800 p-2 shadow-xl ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] rotate-6">
                            <img src="{{ asset('storage/'.$heroExtras['recentProducts'][1]->primary_image) }}" alt="{{ $heroExtras['recentProducts'][1]->name }}" loading="lazy" class="w-full h-full object-cover rounded-2xl">
                        </div>
                    @endif

                    {{-- Main hero card --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-56 sm:w-72 aspect-[4/5] rounded-3xl overflow-hidden bg-white dark:bg-zinc-800 p-2.5 shadow-2xl ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.1] rotate-2 transition-transform duration-500 hover:rotate-0 motion-reduce:transform-none z-10">
                        <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="600" fetchpriority="high" class="w-full h-[calc(100%-2.25rem)] object-cover rounded-2xl">
                        <p class="h-9 flex items-center justify-center text-xs font-bold text-zinc-700 dark:text-zinc-200 line-clamp-1">{{ $product->name ?? $heroTitle }}</p>
                    </div>

                    {{-- Floating review chip --}}
                    @if($heroExtras['spotlightTestimonial'])
                        <div class="absolute bottom-6 left-0 sm:left-2 z-20 max-w-[240px] flex items-center gap-2.5 bg-white/95 dark:bg-zinc-800/95 backdrop-blur rounded-2xl px-3.5 py-2.5 shadow-xl ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.1] -rotate-2">
                            @if($heroExtras['spotlightTestimonial']->image)
                                <img src="{{ asset('storage/'.$heroExtras['spotlightTestimonial']->image) }}" alt="{{ $heroExtras['spotlightTestimonial']->name }}" loading="lazy" class="w-8 h-8 rounded-full object-cover shrink-0">
                            @else
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 text-xs font-bold shrink-0">{{ strtoupper(substr($heroExtras['spotlightTestimonial']->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold text-zinc-900 dark:text-white leading-snug line-clamp-2">{{ $heroExtras['spotlightTestimonial']->content }}</p>
                                @if($heroExtras['spotlightTestimonial']->rating)
                                    <p class="flex items-center gap-0.5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 tabular-nums">
                                        <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        {{ number_format($heroExtras['spotlightTestimonial']->rating, 1) }} — {{ $heroExtras['spotlightTestimonial']->name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
