@props(['testimonials'])

@if($testimonials && $testimonials->count() > 0)
    <section id="testimonials" class="py-4 sm:py-5">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                    {{ __('Testimonials') }}
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                    {{ __('What Our Customers Say') }}
                </h2>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                @foreach($testimonials as $index => $testimonial)
                    <figure
                        x-data="{ shown: false }"
                        x-intersect.once="shown = true"
                        style="transition-delay: {{ min($index, 5) * 75 }}ms"
                        class="group flex flex-col h-full bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 transition-all duration-300 motion-reduce:transition-none hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 motion-reduce:transform-none"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        <div class="flex items-center gap-0.5 mb-4" role="img" aria-label="{{ $testimonial->rating }} {{ __('out of 5 stars') }}">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 {{ $i < $testimonial->rating ? 'text-amber-400' : 'text-zinc-300 dark:text-zinc-600' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <blockquote class="flex-1">
                            <p class="text-sm sm:text-[15px] text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                &ldquo;{{ $testimonial->content }}&rdquo;
                            </p>
                        </blockquote>
                        <figcaption class="flex items-center gap-3 pt-5 mt-5 border-t border-zinc-900/[0.06] dark:border-white/[0.06]">
                            @if($testimonial->image)
                                <img src="{{ asset('storage/'.$testimonial->image) }}" alt="{{ $testimonial->name }}" loading="lazy" class="w-11 h-11 rounded-full object-cover ring-2 ring-white dark:ring-zinc-700 shadow-sm flex-shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-sm font-semibold ring-2 ring-white dark:ring-zinc-700 shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $testimonial->name }}</p>
                                @if($testimonial->location)
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $testimonial->location }}</p>
                                @endif
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
            </div>
        </div>
    </section>
@endif

