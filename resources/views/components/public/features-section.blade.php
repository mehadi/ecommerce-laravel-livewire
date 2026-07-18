@props(['features'])

@if($features && $features->count() > 0)
    <section id="features" class="py-4 sm:py-5">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                    {{ __('Features') }}
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                    {{ __('Why Choose Our Product?') }}
                </h2>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                @foreach($features as $index => $feature)
                    <div
                        x-data="{ shown: false }"
                        x-intersect.once="shown = true"
                        style="transition-delay: {{ min($index, 5) * 75 }}ms"
                        class="group h-full bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 transition-all duration-300 motion-reduce:transition-none hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 motion-reduce:transform-none"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        <div class="space-y-5">
                            @if($feature->image)
                                <div class="overflow-hidden rounded-2xl">
                                    <img src="{{ asset('storage/'.$feature->image) }}" alt="{{ $feature->title }}" loading="lazy" class="w-full h-36 sm:h-40 object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none">
                                </div>
                            @else
                                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 rounded-full flex items-center justify-center group-hover:scale-105 transition-transform duration-300 motion-reduce:transform-none">
                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-display font-semibold text-zinc-900 dark:text-white mb-2 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200">
                                    {{ $feature->title }}
                                </h3>
                                @if($feature->content)
                                    <p class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                        {{ $feature->content }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>
        </div>
    </section>
@endif

