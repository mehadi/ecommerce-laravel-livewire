{{-- Centered Minimal: centered headline + CTA with a wide product banner below. --}}
<section class="relative overflow-hidden pt-24 sm:pt-28 pb-12 sm:pb-16">
    <div class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-[var(--tenant-primary)]/[0.07] rounded-full blur-3xl" aria-hidden="true"></div>

    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="mx-auto max-w-3xl text-center flex flex-col items-center gap-5 sm:gap-6">
            @if($heroBadge)
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-sm">
                    <svg class="w-3 h-3 text-[var(--tenant-primary)]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    {{ $heroBadge }}
                </span>
            @endif

            <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.75rem] font-bold text-zinc-900 dark:text-white leading-[1.05] tracking-tight text-balance break-words">
                {{ $heroTitle }}
            </h1>

            @if($heroContent)
                <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl">{{ $heroContent }}</p>
            @endif

            <div class="flex flex-wrap items-center justify-center gap-3">
                @if($product)
                    <a href="#product" class="inline-flex items-center gap-2 bg-[var(--tenant-primary)] hover:brightness-110 text-white px-8 py-3.5 rounded-full font-bold text-sm sm:text-base shadow-lg transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)] focus-visible:ring-offset-2">
                        {{ __('Order Now') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"></path></svg>
                    </a>
                @endif
                <a href="/shop" wire:navigate class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-bold text-sm sm:text-base text-zinc-800 dark:text-zinc-100 bg-white dark:bg-zinc-800 ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white">
                    {{ __('Browse Shop') }}
                </a>
            </div>
        </div>

        {{-- Wide product banner --}}
        @if($heroImage)
            <div class="relative mt-10 sm:mt-14 mx-auto max-w-5xl">
                <div class="relative rounded-[2rem] bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_16px_40px_-20px_rgb(16_24_40_/_0.15)] px-8 py-10 sm:py-14 flex items-center justify-center overflow-hidden">
                    <div class="pointer-events-none absolute -bottom-24 left-1/2 -translate-x-1/2 w-[480px] h-[280px] bg-[var(--tenant-primary)]/10 rounded-full blur-3xl" aria-hidden="true"></div>
                    <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="480" fetchpriority="high" class="relative max-h-64 sm:max-h-80 w-auto object-contain drop-shadow-2xl">
                </div>

                {{-- Floating stat chips --}}
                <div class="hidden sm:flex absolute top-8 left-6 lg:-left-6 items-center gap-2.5 bg-white dark:bg-zinc-800 rounded-2xl px-4 py-3 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08]">
                    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-[var(--tenant-primary)]/10 text-[var(--tenant-primary)]" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-zinc-900 dark:text-white tabular-nums leading-tight">{{ $heroOrderCountLabel }}</p>
                        <p class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ __('Orders Delivered') }}</p>
                    </div>
                </div>
                @if($heroExtras['testimonialCount'] > 0)
                    <div class="hidden sm:flex absolute bottom-8 right-6 lg:-right-6 items-center gap-2.5 bg-white dark:bg-zinc-800 rounded-2xl px-4 py-3 shadow-lg ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08]">
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-amber-50 dark:bg-amber-900/25 text-amber-500" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-white tabular-nums leading-tight">{{ $heroExtras['avgRating'] }}</p>
                            <p class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ __('reviews') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
