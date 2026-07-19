{{-- Sectioned-steps checkout content — the same single-page form broken into numbered sections. --}}
<form wire:submit.prevent="placeOrder" class="space-y-6">
    @if(!empty($cart))
        <div class="flex gap-4">
            <div class="flex flex-col items-center flex-shrink-0">
                <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">1</span>
                <span class="flex-1 w-px bg-zinc-200 dark:bg-zinc-700 mt-2"></span>
            </div>
            <div class="flex-1 pb-2 min-w-0">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('Review Your Order') }}</h3>
                @include('components.public.checkouts._order-summary', ['style' => 'compact'])
            </div>
        </div>
    @endif

    <div class="flex gap-4">
        <div class="flex flex-col items-center flex-shrink-0">
            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">2</span>
            <span class="flex-1 w-px bg-zinc-200 dark:bg-zinc-700 mt-2"></span>
        </div>
        <div class="flex-1 pb-2 min-w-0 space-y-5">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Contact & Shipping Details') }}</h3>
            @include('components.public.checkouts._contact-fields', ['style' => 'default'])
            @include('components.public.checkouts._address-fields', ['style' => 'default'])
        </div>
    </div>

    <div class="flex gap-4">
        <div class="flex flex-col items-center flex-shrink-0">
            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">3</span>
        </div>
        <div class="flex-1 min-w-0 space-y-3">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Confirm & Place Order') }}</h3>
            @include('components.public.checkouts._submit-button', ['style' => 'default'])
        </div>
    </div>
</form>
