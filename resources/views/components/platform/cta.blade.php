@php
    use App\Models\PlatformSetting;

    $supportEmail = PlatformSetting::get('support_contact_email');
@endphp

<section
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    class="py-4 sm:py-5"
>
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div
            class="relative overflow-hidden rounded-[2rem] bg-zinc-900 dark:bg-zinc-900 p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] transition-all duration-700 motion-reduce:transition-none"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
        >
            {{-- Decorative layers --}}
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.06),transparent_50%)]"></div>
            <div class="pointer-events-none absolute -top-20 -right-20 w-72 h-72 bg-emerald-400/15 rounded-full blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-16 w-72 h-72 bg-teal-400/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-2xl mx-auto text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-400/20 mb-5">
                    {{ __('Ready When You Are') }}
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-tight text-white mb-4 sm:mb-5 tracking-tight text-balance">
                    {{ __('Ready to launch your store?') }}
                </h2>
                <p class="text-base sm:text-lg text-zinc-300 mb-8 sm:mb-10 leading-relaxed max-w-xl mx-auto">
                    {{ __('Create your account, pick a plan, and start selling — free to start, no commitment.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center gap-2.5 bg-emerald-500 hover:bg-emerald-400 text-white px-8 sm:px-10 py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-500/30 hover:shadow-lg hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900">
                        {{ __('Get Started Free') }}
                    </a>
                    @if($supportEmail)
                        <a href="mailto:{{ $supportEmail }}" class="inline-flex items-center justify-center gap-2.5 px-8 sm:px-10 py-4 rounded-full text-base sm:text-lg font-bold text-white ring-1 ring-white/20 hover:bg-white/5 hover:-translate-y-0.5 motion-reduce:transform-none transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            {{ __('Talk to Us') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
