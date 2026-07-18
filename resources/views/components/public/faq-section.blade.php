@props(['faqs'])

@if($faqs && $faqs->count() > 0)
    @push('head')
        @php
            $faqSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqs->map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq->title,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq->content ?? '',
                    ],
                ])->values()->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section id="faq" class="py-4 sm:py-5" aria-labelledby="faq-heading">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-5">
                            {{ __('FAQ') }}
                        </span>
                        <h2 id="faq-heading" class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-tight text-zinc-900 dark:text-white tracking-tight text-balance">
                            {{ __('Frequently Asked Questions') }}
                        </h2>
                    </div>
                </div>
                <div
                    class="lg:col-span-8 divide-y divide-zinc-900/[0.06] dark:divide-white/[0.08] border-t border-b border-zinc-900/[0.06] dark:border-white/[0.08]"
                    x-data="{
                        focusFaq(target) {
                            const btns = [...$el.querySelectorAll('button[aria-controls]')];
                            const i = btns.indexOf(document.activeElement);
                            if (i === -1) return;
                            if (target === 'first') return btns[0].focus();
                            if (target === 'last') return btns[btns.length - 1].focus();
                            btns[(i + target + btns.length) % btns.length].focus();
                        }
                    }"
                >
                    @foreach($faqs as $index => $faq)
                        <div x-data="{ isOpen: {{ $index === 0 ? 'true' : 'false' }} }">
                            <h3>
                                <button
                                    type="button"
                                    id="faq-button-{{ $index }}"
                                    @click="isOpen = !isOpen"
                                    @keydown.arrow-down.prevent="focusFaq(1)"
                                    @keydown.arrow-up.prevent="focusFaq(-1)"
                                    @keydown.home.prevent="focusFaq('first')"
                                    @keydown.end.prevent="focusFaq('last')"
                                    :aria-expanded="isOpen ? 'true' : 'false'"
                                    aria-controls="faq-panel-{{ $index }}"
                                    class="group w-full flex items-center justify-between gap-5 text-left py-5 sm:py-6 cursor-pointer touch-manipulation rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-4 dark:focus-visible:ring-offset-zinc-900"
                                >
                                    <span
                                        class="text-base sm:text-lg font-semibold flex-1 text-balance transition-colors duration-200 motion-reduce:transition-none"
                                        :class="isOpen ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-900 dark:text-white group-hover:text-zinc-600 dark:group-hover:text-zinc-300'"
                                    >
                                        {{ $faq->title }}
                                    </span>
                                    <span
                                        class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center ring-1 transition-all duration-200 motion-reduce:transition-none"
                                        :class="isOpen
                                            ? 'bg-emerald-600 ring-emerald-600 text-white rotate-45'
                                            : 'bg-white dark:bg-zinc-900 ring-zinc-900/[0.10] dark:ring-white/[0.14] text-zinc-500 dark:text-zinc-400 group-hover:ring-zinc-900/[0.20] dark:group-hover:ring-white/[0.25]'"
                                        aria-hidden="true"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </span>
                                </button>
                            </h3>
                            <div
                                x-show="isOpen"
                                x-collapse
                                id="faq-panel-{{ $index }}"
                                role="region"
                                aria-labelledby="faq-button-{{ $index }}"
                            >
                                @if($faq->content)
                                    <p class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-400 leading-relaxed text-pretty pb-6 sm:pb-7 pr-14 max-w-prose">
                                        {!! nl2br(e($faq->content)) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            </div>
        </div>
    </section>
@endif
