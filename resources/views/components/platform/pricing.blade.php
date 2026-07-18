@props(['plans'])

@php
    // Common 3-tier convention: the middle plan (typically the mid-priced upsell
    // target between a free/entry tier and the top-end tier) gets the "Most
    // popular" highlight, not the cheapest or the most expensive.
    $popularIndex = $plans->count() >= 2 ? intdiv($plans->count(), 2) : null;
@endphp

<section id="pricing" class="py-4 sm:py-5">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                {{ __('Pricing') }}
            </span>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                {{ __('Simple pricing that grows with you') }}
            </h2>
            <p class="mt-4 text-base sm:text-lg text-zinc-500 dark:text-zinc-400">
                {{ __('Start free. Upgrade whenever you need more room.') }}
            </p>
        </div>

        @if($plans->isEmpty())
            <p class="text-center text-zinc-500 dark:text-zinc-400">{{ __('Pricing is being finalized — check back soon.') }}</p>
        @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-{{ min($plans->count(), 3) }} gap-6 sm:gap-8 items-start max-w-5xl mx-auto">
            @foreach($plans as $index => $plan)
                @php
                    $isPopular = $index === $popularIndex;
                    $priceValue = (float) $plan->price;
                @endphp
                <div
                    x-data="{ shown: false }"
                    x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 75 }}ms"
                    class="relative h-full flex flex-col rounded-3xl p-6 sm:p-8 transition-all duration-500 motion-reduce:transition-none {{ $isPopular
                        ? 'bg-zinc-900 dark:bg-emerald-950/40 ring-2 ring-emerald-500 shadow-[0_12px_32px_-8px_rgba(16,185,129,0.35)] sm:-translate-y-2'
                        : 'bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]' }}"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
                    @if($isPopular)
                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-1 rounded-full bg-emerald-500 text-white text-xs font-bold uppercase tracking-wide shadow-md">
                            {{ __('Most Popular') }}
                        </span>
                    @elseif($plan->is_default)
                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-1 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-xs font-bold uppercase tracking-wide shadow-md">
                            {{ __('Free to start') }}
                        </span>
                    @endif

                    <h3 class="text-lg font-display font-semibold {{ $isPopular ? 'text-white' : 'text-zinc-900 dark:text-white' }}">
                        {{ $plan->name }}
                    </h3>

                    <div class="mt-4 flex items-baseline gap-1">
                        <span class="font-display text-4xl font-bold tabular-nums {{ $isPopular ? 'text-white' : 'text-zinc-900 dark:text-white' }}">
                            {{ $priceValue == 0 ? __('Free') : '$'.number_format($priceValue, 0) }}
                        </span>
                        @if($priceValue > 0)
                            <span class="text-sm {{ $isPopular ? 'text-zinc-300' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $plan->billing_period === 'yearly' ? __('/yr') : __('/mo') }}</span>
                        @endif
                    </div>

                    <ul class="mt-6 space-y-3 flex-1">
                        @foreach($plan->highlights() as $highlight)
                            <li class="flex items-start gap-2.5 text-sm {{ $isPopular ? 'text-zinc-200' : 'text-zinc-600 dark:text-zinc-400' }}">
                                <svg class="w-5 h-5 shrink-0 {{ $isPopular ? 'text-emerald-400' : 'text-emerald-600 dark:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ $highlight }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <a
                        href="{{ route('register') }}"
                        wire:navigate
                        class="mt-8 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full font-bold text-sm transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $isPopular
                            ? 'bg-emerald-500 hover:bg-emerald-400 text-white shadow-md shadow-emerald-500/30 focus-visible:ring-emerald-400 focus-visible:ring-offset-zinc-900'
                            : 'bg-zinc-900 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-100 text-white dark:text-zinc-900 focus-visible:ring-zinc-900 dark:focus-visible:ring-white' }}"
                    >
                        {{ $priceValue == 0 ? __('Start Free') : __('Choose :plan', ['plan' => $plan->name]) }}
                    </a>
                </div>
            @endforeach
        </div>
        @endif
        </div>
    </div>
</section>
