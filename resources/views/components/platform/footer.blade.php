@php
    use App\Models\PlatformSetting;

    $siteName = config('app.name');
    $supportEmail = PlatformSetting::get('support_contact_email');
@endphp

<footer class="relative bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-t border-zinc-900/[0.04] dark:border-white/[0.06] pt-16 sm:pt-20 pb-10 overflow-hidden">
    {{-- Subtle brand glow --}}
    <div class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-emerald-500/[0.05] rounded-full blur-3xl"></div>

    <div class="relative container mx-auto px-4 sm:px-6 frontend-container">
        <div class="grid gap-12 md:gap-8 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr] mb-14">
            <div class="space-y-5">
                <a href="{{ route('platform.home') }}" wire:navigate class="inline-flex items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900">
                    <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md shadow-emerald-600/20">
                        <span class="text-white font-display font-bold text-lg">{{ substr($siteName, 0, 1) }}</span>
                    </div>
                    <span class="font-display text-xl font-semibold tracking-tight">{{ $siteName }}</span>
                </a>
                <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed text-sm sm:text-[15px] max-w-xs">
                    {{ __('Everything you need to build, launch, and grow your online store.') }}
                </p>
            </div>
            <nav aria-label="{{ __('Product') }}">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Product') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    <li><a href="#features" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Features') }}</a></li>
                    <li><a href="#pricing" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Pricing') }}</a></li>
                    <li><a href="#faq" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('FAQ') }}</a></li>
                </ul>
            </nav>
            <nav aria-label="{{ __('Account') }}">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Account') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    <li><a href="{{ route('login') }}" wire:navigate class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Login') }}</a></li>
                    <li><a href="{{ route('register') }}" wire:navigate class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Get Started') }}</a></li>
                </ul>
            </nav>
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Legal') }}</h4>
                <ul class="space-y-3.5 text-[15px] mb-6">
                    <li><a href="#" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="#" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Terms of Service') }}</a></li>
                </ul>
                @if($supportEmail)
                    <a href="mailto:{{ $supportEmail }}" class="flex items-center gap-3 text-[15px] text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        <span class="break-all">{{ $supportEmail }}</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="pt-8 border-t border-zinc-900/[0.06] dark:border-white/[0.08] text-center text-sm text-zinc-500 dark:text-zinc-400">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}
        </div>
    </div>
</footer>
