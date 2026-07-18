@php
    $steps = [
        [
            'title' => __('Create your account'),
            'content' => __('Sign up and pick a plan — start free on Starter, upgrade whenever you outgrow it.'),
        ],
        [
            'title' => __('Customize your storefront'),
            'content' => __('Add products, build your landing page, set up navigation, and match your brand colors.'),
        ],
        [
            'title' => __('Go live on your domain'),
            'content' => __('Launch on a free subdomain or connect your own custom domain and start taking orders.'),
        ],
    ];
@endphp

<section class="py-4 sm:py-5">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                {{ __('How It Works') }}
            </span>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                {{ __('From sign-up to sold, in three steps') }}
            </h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-6 sm:gap-8">
            @foreach($steps as $index => $step)
                <div
                    x-data="{ shown: false }"
                    x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    class="relative bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] transition-all duration-500 motion-reduce:transition-none"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
                    <span class="font-display text-4xl sm:text-5xl font-bold text-emerald-600/20 dark:text-emerald-400/20 tabular-nums leading-none">
                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <h3 class="mt-4 text-lg font-display font-semibold text-zinc-900 dark:text-white">
                        {{ $step['title'] }}
                    </h3>
                    <p class="mt-2 text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {{ $step['content'] }}
                    </p>

                    @if(!$loop->last)
                        <svg class="hidden sm:block absolute top-1/2 -right-4 -translate-y-1/2 w-8 h-8 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25"></path>
                        </svg>
                    @endif
                </div>
            @endforeach
        </div>
        </div>
    </div>
</section>
