@php
    // Swatch palette cycling like the "Popular Colors" dots in the reference design
    $sizeChipColors = ['bg-blue-500', 'bg-orange-400', 'bg-emerald-500', 'bg-red-500', 'bg-teal-400'];
@endphp

<section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5">
    {{-- Ambient glows on the grey-blue canvas --}}
    <div class="pointer-events-none absolute -top-24 -left-24 w-96 h-96 bg-white/40 dark:bg-white/[0.02] rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -right-24 w-[500px] h-[500px] bg-slate-400/20 dark:bg-white/[0.02] rounded-full blur-3xl"></div>

    {{-- Ivory shell containing navbar space + bento grid --}}
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
    <div class="relative rounded-[2rem] bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] p-4 sm:p-5 lg:p-6 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[6.75rem]">
        <div class="grid lg:grid-cols-12 gap-4 sm:gap-5">
            {{-- ============ LEFT: main hero card + bottom cards ============ --}}
            <div class="lg:col-span-9 flex flex-col gap-4 sm:gap-5 min-w-0">
                {{-- Main hero card --}}
                <div class="relative flex-1 overflow-hidden rounded-3xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8 lg:p-10">
                    <div class="relative z-10 grid {{ $heroImage ? 'sm:grid-cols-[1.05fr_0.95fr]' : '' }} gap-8 sm:gap-6 items-center h-full">
                        {{-- Copy column --}}
                        <div class="flex flex-col gap-5 sm:gap-6">
                            @if($heroBadge)
                                <div class="inline-flex items-center gap-2 self-start bg-white/90 dark:bg-zinc-700/80 backdrop-blur-sm px-3.5 py-1.5 rounded-full text-xs font-semibold text-zinc-600 dark:text-zinc-200 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] shadow-sm">
                                    <svg class="w-3 h-3 text-zinc-800 dark:text-zinc-300" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                        <rect x="1" y="1" width="6" height="6" rx="1.5"></rect>
                                        <rect x="9" y="1" width="6" height="6" rx="1.5"></rect>
                                        <rect x="1" y="9" width="6" height="6" rx="1.5"></rect>
                                        <rect x="9" y="9" width="6" height="6" rx="1.5"></rect>
                                    </svg>
                                    {{ $heroBadge }}
                                </div>
                            @endif

                            <h1 class="font-display text-4xl sm:text-[2.75rem] lg:text-[3.5rem] font-bold text-zinc-900 dark:text-white leading-[1.05] tracking-tight text-balance break-words">
                                {{ $heroTitle }}
                            </h1>

                            @if($heroContent)
                                <div class="flex items-start gap-4 sm:gap-5 max-w-md">
                                    <span class="font-display text-3xl sm:text-4xl font-light text-zinc-300 dark:text-zinc-600 tabular-nums leading-none">01</span>
                                    <svg class="mt-3.5 w-12 sm:w-14 shrink-0 text-zinc-400 dark:text-zinc-500" viewBox="0 0 56 8" fill="none" aria-hidden="true">
                                        <path d="M0 4h52m0 0-4-3m4 3-4 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <div class="min-w-0">
                                        @if($product)
                                            <p class="text-sm font-bold text-zinc-900 dark:text-white mb-1">{{ $product->name }}</p>
                                        @endif
                                        <p class="text-sm sm:text-[0.9rem] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                            {{ $heroContent }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($product)
                                <a href="#product" class="group inline-flex items-center gap-3 self-start bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white pl-6 pr-1.5 py-1.5 rounded-full font-bold text-sm sm:text-base transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                                    {{ __('Order Now') }}
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-zinc-900 dark:bg-black text-white transition-transform duration-300 group-hover:rotate-45 motion-reduce:transform-none">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9"></path>
                                        </svg>
                                    </span>
                                </a>
                            @endif

                            <div class="mt-auto pt-4">
                                @include('components.public.heroes._social')
                            </div>
                        </div>

                        {{-- Product visual with floating spheres + arc --}}
                        @if($heroImage)
                            <div class="relative self-stretch flex items-center justify-center min-h-[240px] sm:min-h-[300px]">
                                {{-- Floating spheres --}}
                                <span class="absolute top-2 left-[12%] w-3.5 h-3.5 rounded-full bg-gradient-to-br from-blue-800 to-blue-950 shadow-md animate-float motion-reduce:animate-none" aria-hidden="true"></span>
                                <span class="absolute top-[18%] right-[6%] w-2.5 h-2.5 rounded-full bg-gradient-to-br from-blue-700 to-blue-900 shadow animate-float-delayed motion-reduce:animate-none" aria-hidden="true"></span>
                                <span class="absolute top-[45%] left-0 w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600 shadow-sm animate-float-delayed motion-reduce:animate-none" aria-hidden="true"></span>
                                <span class="absolute bottom-[20%] right-[14%] w-3 h-3 rounded-full bg-gradient-to-br from-blue-900 to-zinc-900 shadow-md animate-float motion-reduce:animate-none" aria-hidden="true"></span>
                                <span class="absolute bottom-[30%] left-[8%] w-2.5 h-2.5 rounded-full bg-gradient-to-br from-zinc-400 to-zinc-500 shadow animate-float motion-reduce:animate-none" aria-hidden="true"></span>

                                {{-- Curved arc under product --}}
                                <svg class="pointer-events-none absolute -bottom-3 left-1/2 -translate-x-1/2 w-[115%] max-w-none h-20 text-zinc-900/15 dark:text-white/15" viewBox="0 0 520 80" fill="none" preserveAspectRatio="none" aria-hidden="true">
                                    <path d="M6 6 C 150 88, 370 88, 514 6" stroke="currentColor" stroke-width="1.5"></path>
                                </svg>
                                <span class="absolute -bottom-1 left-1/2 translate-x-8 flex items-center justify-center w-9 h-9 rounded-full bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.1] shadow-md text-zinc-600 dark:text-zinc-300" aria-hidden="true">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25"></path>
                                    </svg>
                                </span>

                                <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $product->name ?? ($heroTitle ?? '') }}" width="480" height="480" fetchpriority="high" class="relative max-h-60 sm:max-h-80 w-auto object-contain drop-shadow-2xl transform hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none">
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bottom card row --}}
                <div class="grid sm:grid-cols-2 lg:grid-cols-[1.05fr_0.85fr_1.6fr] gap-4 sm:gap-5">
                    {{-- More products --}}
                    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-5 sm:p-6 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('Our Products') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 tabular-nums">{{ $heroExtras['productCount'] }}{{ __('+ items') }}</p>
                            </div>
                            <span class="flex items-center justify-center w-8 h-8 shrink-0 rounded-full bg-red-50 dark:bg-red-900/25 text-red-500" aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001z"></path>
                                </svg>
                            </span>
                        </div>
                        @if($heroExtras['recentProducts']->count() > 0)
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($heroExtras['recentProducts'] as $recentProduct)
                                    <div class="aspect-square rounded-2xl bg-zinc-100 dark:bg-zinc-700/60 ring-1 ring-zinc-900/[0.03] dark:ring-white/[0.05] overflow-hidden">
                                        <img src="{{ asset('storage/'.$recentProduct->primary_image) }}" alt="{{ $recentProduct->name }}" loading="lazy" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Orders + rating --}}
                    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-5 sm:p-6 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] flex flex-col items-center justify-center text-center">
                        @if($heroExtras['recentProducts']->count() > 0)
                            <div class="flex items-center -space-x-2.5 mb-3">
                                @foreach($heroExtras['recentProducts'] as $recentProduct)
                                    <img src="{{ asset('storage/'.$recentProduct->primary_image) }}" alt="{{ $recentProduct->name }}" loading="lazy" class="w-9 h-9 rounded-full object-cover ring-2 ring-zinc-50 dark:ring-zinc-800">
                                @endforeach
                            </div>
                        @endif
                        <div class="w-full rounded-2xl bg-blue-500 text-white px-3 py-2.5 shadow-md shadow-blue-500/20">
                            <p class="font-display text-lg sm:text-xl font-bold leading-tight tabular-nums">{{ $heroOrderCountLabel }}</p>
                            <p class="text-[10px] font-semibold text-blue-100 uppercase tracking-wide">{{ __('Orders Delivered') }}</p>
                        </div>
                        @if($heroExtras['testimonialCount'] > 0)
                            <p class="mt-2.5 text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1 tabular-nums">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                {{ $heroExtras['avgRating'] }} {{ __('reviews') }}
                            </p>
                        @endif
                    </div>

                    {{-- Testimonial spotlight (wide) --}}
                    @if($heroExtras['spotlightTestimonial'])
                        <div class="relative sm:col-span-2 lg:col-span-1 bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-5 sm:p-6 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] flex gap-4">
                            <a href="#testimonials" class="absolute top-3.5 right-3.5 z-10 flex items-center justify-center w-10 h-10 rounded-full bg-white/90 dark:bg-zinc-700/90 backdrop-blur ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.1] shadow-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-900 hover:text-white dark:hover:bg-white dark:hover:text-zinc-900 hover:rotate-45 motion-reduce:transform-none transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white" aria-label="{{ __('Customer reviews') }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9"></path>
                                </svg>
                            </a>

                            <div class="flex-1 min-w-0 flex flex-col gap-2.5 {{ $heroExtras['spotlightTestimonial']->image ? '' : 'pr-10' }}">
                                <span class="inline-flex items-center gap-1 self-start px-2.5 py-1 rounded-full bg-orange-50 dark:bg-orange-900/25 text-orange-600 dark:text-orange-400 text-[10px] font-bold uppercase tracking-wide ring-1 ring-orange-600/10 dark:ring-orange-500/20">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.177A7.547 7.547 0 0 1 6.648 6.61a.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Popular') }}
                                </span>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white leading-snug line-clamp-2">{{ $heroExtras['spotlightTestimonial']->content }}</p>
                                <div class="mt-auto flex items-center gap-2">
                                    @if($heroExtras['spotlightTestimonial']->image)
                                        <img src="{{ asset('storage/'.$heroExtras['spotlightTestimonial']->image) }}" alt="{{ $heroExtras['spotlightTestimonial']->name }}" loading="lazy" class="w-7 h-7 rounded-full object-cover ring-2 ring-zinc-50 dark:ring-zinc-800 flex-shrink-0">
                                    @else
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($heroExtras['spotlightTestimonial']->name, 0, 1)) }}
                                        </span>
                                    @endif
                                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">{{ $heroExtras['spotlightTestimonial']->name }}</span>
                                </div>
                            </div>

                            @if($heroExtras['spotlightTestimonial']->image)
                                <div class="relative w-24 sm:w-28 shrink-0 rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-700/60 min-h-[6rem]">
                                    <img src="{{ asset('storage/'.$heroExtras['spotlightTestimonial']->image) }}" alt="{{ $heroExtras['spotlightTestimonial']->name }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover">
                                    @if($heroExtras['spotlightTestimonial']->rating)
                                        <span class="absolute bottom-1.5 right-1.5 inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-white/90 dark:bg-zinc-900/90 backdrop-blur text-[10px] font-bold text-zinc-900 dark:text-white tabular-nums">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            {{ number_format($heroExtras['spotlightTestimonial']->rating, 1) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- ============ RIGHT: stacked sidebar cards ============ --}}
            <div class="lg:col-span-3 flex flex-col gap-4 sm:gap-5 min-w-0">
                {{-- Size dots ("Popular Colors" slot) --}}
                @if($heroExtras['weightValues']->count() > 0)
                    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-5 sm:p-6 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                        <p class="text-sm font-bold text-zinc-900 dark:text-white mb-3">{{ __('Available Sizes') }}</p>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($heroExtras['weightValues'] as $value)
                                <span class="inline-flex items-center justify-center min-w-10 h-10 px-1.5 rounded-full {{ $sizeChipColors[$loop->index % count($sizeChipColors)] }} text-white text-[10px] font-bold ring-[3px] ring-zinc-50 dark:ring-zinc-800 shadow-md">
                                    {{ $value->display_value }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Spotlight product ("New Gen X-Bud" slot) --}}
                @if($heroExtras['spotlightProduct'])
                    <a href="#product" class="group block bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-5 sm:p-6 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] transition-shadow duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white">
                        <p class="text-sm font-bold text-zinc-900 dark:text-white leading-snug line-clamp-2 mb-2">{{ $heroExtras['spotlightProduct']->name }}</p>
                        <img src="{{ asset('storage/'.$heroExtras['spotlightProduct']->primary_image) }}" alt="{{ $heroExtras['spotlightProduct']->name }}" loading="lazy" class="w-full h-24 object-contain drop-shadow-lg transition-transform duration-500 group-hover:scale-105 motion-reduce:transform-none">
                        <span class="mt-3 inline-flex items-center justify-center w-9 h-9 rounded-full ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.12] text-zinc-700 dark:text-zinc-200 group-hover:bg-zinc-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-zinc-900 group-hover:rotate-45 motion-reduce:transform-none transition-all duration-300" aria-hidden="true">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9"></path>
                            </svg>
                        </span>
                    </a>
                @endif

                {{-- Tall category image card --}}
                @if($heroExtras['spotlightCategory'])
                    <a href="{{ '/category/'.$heroExtras['spotlightCategory']->slug }}" wire:navigate class="group relative flex-1 min-h-[240px] sm:min-h-[280px] rounded-3xl overflow-hidden ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white">
                        <img src="{{ asset('storage/'.$heroExtras['spotlightCategory']->image) }}" alt="{{ $heroExtras['spotlightCategory']->name }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 motion-reduce:transform-none">
                        <span class="absolute top-3.5 right-3.5 flex items-center justify-center w-9 h-9 rounded-full bg-white/90 dark:bg-zinc-900/80 backdrop-blur ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.1] shadow-sm text-zinc-700 dark:text-zinc-200 group-hover:rotate-45 motion-reduce:transform-none transition-transform duration-300" aria-hidden="true">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9"></path>
                            </svg>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent" aria-hidden="true"></span>
                        <span class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 block">
                            <span class="block text-white font-display font-bold text-base sm:text-lg leading-tight">{{ $heroExtras['spotlightCategory']->name }}</span>
                            <span class="block text-white/80 text-xs mt-0.5">{{ __('Farm fresh, naturally sourced') }}</span>
                        </span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    </div>
</section>
