{{-- Compact checkout content — condensed spacing and smaller fields, built for a slide-in panel. --}}
@if(!empty($cart))
    @include('components.public.checkouts._order-summary', ['style' => 'compact'])
@endif

<div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl p-5">
    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Your Information') }}</h3>

    <form wire:submit.prevent="placeOrder" class="space-y-4">
        @include('components.public.checkouts._contact-fields', ['style' => 'compact'])
        @include('components.public.checkouts._address-fields', ['style' => 'compact'])
        @include('components.public.checkouts._submit-button', ['style' => 'compact'])
    </form>
</div>
