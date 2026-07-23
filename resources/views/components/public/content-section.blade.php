@props(['sections', 'heading'])

@php
    $eyebrows = [
        'about' => __('About'),
        'benefits' => __('Benefits'),
        'contact' => __('Contact'),
        'products' => __('Products'),
    ];

    $eyebrow = $eyebrows[$heading] ?? ucfirst($heading);
@endphp

@if($sections && $sections->count() > 0)
    <section class="py-4 sm:py-5">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] space-y-12 sm:space-y-16">
                @foreach($sections as $index => $section)
                    <div class="grid md:grid-cols-2 gap-8 sm:gap-12 items-center {{ $index % 2 === 1 ? 'md:[&>*:first-child]:order-2' : '' }}">
                        @if($section->image)
                            <div class="overflow-hidden rounded-3xl">
                                <img src="{{ asset('storage/'.$section->image) }}" alt="{{ $section->title }}" loading="lazy" class="w-full h-64 sm:h-80 object-cover">
                            </div>
                        @endif

                        <div class="space-y-4 {{ $section->image ? '' : 'md:col-span-2 max-w-2xl mx-auto text-center' }}">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                                {{ $eyebrow }}
                            </span>

                            @if($section->title)
                                <h2 class="font-display text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                                    {{ $section->title }}
                                </h2>
                            @endif

                            @if($section->content)
                                <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed whitespace-pre-line">
                                    {{ $section->content }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
