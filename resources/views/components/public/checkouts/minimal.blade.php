{{-- Minimal checkout content — plain dividers instead of cards, quiet and distraction-free. --}}
@if(!empty($cart))
    @include('components.public.checkouts._order-summary', ['style' => 'plain'])
@endif

<div>
    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4 pt-2 border-t border-zinc-200 dark:border-zinc-800">{{ __('Your Information') }}</h3>

    <form wire:submit.prevent="placeOrder" class="space-y-4">
        @include('components.public.checkouts._contact-fields', ['style' => 'plain'])
        @include('components.public.checkouts._address-fields', ['style' => 'plain'])
        @include('components.public.checkouts._submit-button', ['style' => 'plain'])
    </form>
</div>
