{{-- Gradient Glow: dark brand-gradient panel with glowing product visual. --}}
<section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[var(--tenant-secondary)] via-zinc-900 to-zinc-950 ring-1 ring-white/[0.06] shadow-[0_16px_40px_-20px_rgb(16_24_40_/_0.4)] p-6 sm:p-10 lg:p-14 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[7rem]">
            {{-- Glow blobs --}}
            <div class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 bg-[var(--tenant-primary)]/25 rounded-full blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-24 w-80 h-80 bg-[var(--tenant-primary)]/15 rounded-full blur-3xl" aria-hidden="true"></div>

            <div class="relative grid {{ $heroImage ? 'lg:grid-cols-[1.1fr_0.9fr]' : '' }} gap-10 lg:gap-8 items-center">
                {{-- Copy column --}}
                <div class="flex flex-col gap-5 sm:gap-6">
                    @if($heroBadge)
                        <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur text-xs font-semibold text-white/90 ring-1 ring-white/15">
                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--tenant-primary)] shadow-[0_0_8px_var(--tenant-primary)]" aria-hidden="true"></span>
                            {{ $heroBadge }}
                        </span>
                    @endif

                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.05] tracking-tight text-balance break-words">
                        {{ $heroTitle }}
                    </h1>

                    @if($heroContent)
                        <p class="text-base sm:text-lg text-white/70 leading-relaxed max-w-xl">{{ $heroContent }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-3">
                        @if($product)
                            <a href="{{ $heroPrimaryCtaUrl }}" class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-7 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg shadow-black/30 transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900">
                                {{ $heroPrimaryCtaLabel }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                            </a>
                        @endif
                        <a href="{{ $heroSecondaryCtaUrl }}" @if($heroSecondaryCtaNavigate) wire:navigate @endif class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-bold text-sm sm:text-base text-white/90 bg-white/10 ring-1 ring-white/15 hover:bg-white/20 transition-colors backdrop-blur focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            {{ $heroSecondaryCtaLabel }}
                        </a>
                    </div>

                    {{-- Avatars + orders strip --}}
                    @if($heroShowStats)
                    <div class="flex items-center gap-3 pt-2">
                        @if($heroExtras['recentProducts']->count() > 0)
                            <div class="flex items-center -space-x-2.5">
                                @foreach($heroExtras['recentProducts'] as $recentProduct)
                                    <img src="{{ asset('storage/'.$recentProduct->primary_image) }}" alt="{{ $recentProduct->name }}" loading="lazy" class="w-9 h-9 rounded-full object-cover ring-2 ring-zinc-900">
                                @endforeach
                            </div>
                        @endif
                        <p class="text-sm text-white/70"><span class="font-bold text-white tabular-nums">{{ $heroOrderCountLabel }}</span> {{ __('Orders Delivered') }}</p>
                    </div>
                    @endif

                    @include('components.public.heroes._social', ['tone' => 'dark'])
                </div>

                {{-- Glowing product visual --}}
                @if($heroImage)
                    <div class="relative flex items-center justify-center min-h-[260px] sm:min-h-[340px]">
                        <span class="absolute w-64 h-64 sm:w-80 sm:h-80 rounded-full bg-[var(--tenant-primary)]/30 blur-3xl" aria-hidden="true"></span>
                        <span class="absolute w-56 h-56 sm:w-72 sm:h-72 rounded-full border border-white/10" aria-hidden="true"></span>
                        <span class="absolute w-72 h-72 sm:w-[22rem] sm:h-[22rem] rounded-full border border-white/[0.05]" aria-hidden="true"></span>
                        <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="480" fetchpriority="high" class="relative max-h-64 sm:max-h-80 w-auto object-contain drop-shadow-[0_25px_35px_rgb(0_0_0_/_0.5)] transition-transform duration-500 hover:scale-[1.04] motion-reduce:transform-none">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
