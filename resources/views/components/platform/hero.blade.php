@php
    $siteName = config('app.name');
@endphp

<section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5">
    {{-- Ambient glows --}}
    <div class="pointer-events-none absolute -top-24 -left-24 w-96 h-96 bg-emerald-400/10 dark:bg-white/[0.02] rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -right-24 w-[500px] h-[500px] bg-slate-400/20 dark:bg-white/[0.02] rounded-full blur-3xl"></div>

    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="relative rounded-[2rem] bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] p-4 sm:p-5 lg:p-6 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[6.75rem]">
            <div class="relative rounded-3xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-10 lg:p-14">
                <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
                    {{-- Copy column --}}
                    <div class="flex flex-col gap-6 sm:gap-7">
                        <div class="inline-flex items-center gap-2 self-start bg-emerald-50 dark:bg-emerald-900/30 px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-widest text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            {{ __('Launch in minutes') }}
                        </div>

                        <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.5rem] font-bold text-zinc-900 dark:text-white leading-[1.05] tracking-tight text-balance">
                            {{ __('Build your online store on :name', ['name' => $siteName]) }}
                        </h1>

                        <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-lg">
                            {{ __('Landing pages, a full product catalog, custom domains, and secure checkout — everything you need to sell online, in one platform. No coding required.') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <a href="{{ route('register') }}" wire:navigate class="group inline-flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white pl-6 pr-1.5 py-1.5 rounded-full font-bold text-base transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                                {{ __('Get Started Free') }}
                                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-zinc-900 dark:bg-black text-white transition-transform duration-300 group-hover:rotate-45 motion-reduce:transform-none">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9"></path>
                                    </svg>
                                </span>
                            </a>
                            <a href="#pricing" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full font-bold text-base text-zinc-700 dark:text-zinc-200 ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.12] hover:bg-white dark:hover:bg-white/[0.06] hover:-translate-y-0.5 motion-reduce:transform-none transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                {{ __('See Pricing') }}
                            </a>
                        </div>

                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Free to start on the Starter plan · Upgrade any time') }}
                        </p>
                    </div>

                    {{-- Abstract storefront/dashboard mockup --}}
                    <div class="relative self-stretch flex items-center justify-center min-h-[320px] sm:min-h-[380px]">
                        {{-- Floating spheres --}}
                        <span class="absolute top-0 left-[10%] w-3.5 h-3.5 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-md animate-float motion-reduce:animate-none" aria-hidden="true"></span>
                        <span class="absolute top-[15%] right-[4%] w-2.5 h-2.5 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow animate-float-delayed motion-reduce:animate-none" aria-hidden="true"></span>
                        <span class="absolute bottom-[18%] left-[2%] w-3 h-3 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 shadow-md animate-float motion-reduce:animate-none" aria-hidden="true"></span>

                        {{-- Browser chrome mockup card --}}
                        <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_20px_48px_-16px_rgb(16_24_40_/_0.25)] overflow-hidden">
                            <div class="flex items-center gap-1.5 px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-b border-zinc-900/[0.04] dark:border-white/[0.06]">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <span class="ml-3 flex-1 h-5 rounded-full bg-white dark:bg-zinc-700"></span>
                            </div>
                            <div class="p-4 sm:p-5 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="h-3 w-20 rounded-full bg-zinc-200 dark:bg-zinc-700"></span>
                                    <div class="flex gap-1.5">
                                        <span class="h-6 w-6 rounded-full bg-zinc-100 dark:bg-zinc-700"></span>
                                        <span class="h-6 w-6 rounded-full bg-emerald-500"></span>
                                    </div>
                                </div>
                                <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 h-24 sm:h-28 flex items-end p-3">
                                    <span class="h-2.5 w-24 rounded-full bg-white/70"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2.5">
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="rounded-lg bg-zinc-100 dark:bg-zinc-800 aspect-square flex flex-col items-center justify-center gap-1.5 p-2">
                                            <span class="w-full flex-1 rounded-md bg-zinc-200 dark:bg-zinc-700"></span>
                                            <span class="h-1.5 w-3/4 rounded-full bg-zinc-200 dark:bg-zinc-700"></span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        {{-- Floating stat chips --}}
                        <div class="absolute -bottom-4 -left-2 sm:left-2 bg-white dark:bg-zinc-900 rounded-2xl ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-lg px-4 py-3 flex items-center gap-2.5 animate-float motion-reduce:animate-none">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">{{ __('Custom domains') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Your brand, your URL') }}</p>
                            </div>
                        </div>

                        <div class="absolute -top-4 -right-2 sm:right-2 bg-white dark:bg-zinc-900 rounded-2xl ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-lg px-4 py-3 flex items-center gap-2.5 animate-float-delayed motion-reduce:animate-none">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">{{ __('Secure checkout') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Built in, ready to sell') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
