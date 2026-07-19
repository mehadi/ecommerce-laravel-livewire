{{--
    Split checkout content — order summary and the customer form side by
    side on wide screens. Tailwind's `lg:` breakpoint keys off the browser
    viewport, not this panel's own width, so when the shell is the narrow
    slide-in panel we force a single column instead of squeezing two.
--}}
<div class="grid {{ ($checkoutIsPanel ?? false) ? 'grid-cols-1' : 'lg:grid-cols-2' }} gap-6 items-start">
    <div class="space-y-6">
        @if(!empty($cart))
            @include('components.public.checkouts._order-summary', ['style' => 'default'])
        @endif
    </div>

    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
        <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-5 flex items-center gap-2.5">
            <span class="flex items-center justify-center w-8 h-8 bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] rounded-full">
                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </span>
            {{ __('Your Information') }}
        </h3>

        <form wire:submit.prevent="placeOrder" class="space-y-5">
            @include('components.public.checkouts._contact-fields', ['style' => 'default'])
            @include('components.public.checkouts._address-fields', ['style' => 'default'])
            @include('components.public.checkouts._submit-button', ['style' => 'default'])
        </form>
    </div>
</div>
