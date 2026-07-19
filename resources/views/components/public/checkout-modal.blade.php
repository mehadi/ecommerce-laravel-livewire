@php
    use App\Models\Setting;
    use App\Support\CheckoutVariants;

    $checkoutIsPanel = Setting::get('checkout_display_mode', 'modal') === 'panel';
    $checkoutVariant = CheckoutVariants::resolve(Setting::get('storefront_checkout_variant'));
    $checkoutIsWide = CheckoutVariants::isWide($checkoutVariant);
@endphp

@if($showCheckout ?? false)
    <div
        x-data="{ show: @entangle('showCheckout') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 flex {{ $checkoutIsPanel ? 'justify-end' : 'items-center justify-center p-3 sm:p-4' }}"
        @click.self="$wire.set('showCheckout', false)"
        @keydown.escape.window="$wire.set('showCheckout', false)"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Checkout') }}"
    >
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="{{ $checkoutIsPanel ? 'translate-x-full' : 'opacity-0 scale-95 translate-y-4' }}"
            x-transition:enter-end="{{ $checkoutIsPanel ? 'translate-x-0' : 'opacity-100 scale-100 translate-y-0' }}"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="{{ $checkoutIsPanel ? 'translate-x-0' : 'opacity-100 scale-100 translate-y-0' }}"
            x-transition:leave-end="{{ $checkoutIsPanel ? 'translate-x-full' : 'opacity-0 scale-95 translate-y-4' }}"
            class="bg-white dark:bg-zinc-900 flex flex-col shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden {{ $checkoutIsPanel ? 'h-full w-full sm:max-w-lg' : ($checkoutIsWide ? 'rounded-3xl max-w-4xl w-full max-h-[92vh]' : 'rounded-3xl max-w-2xl w-full max-h-[92vh]') }}"
        >
            <!-- Custom Scrollbar Container -->
            <style>
                .checkout-scroll::-webkit-scrollbar {
                    width: 8px;
                }
                .checkout-scroll::-webkit-scrollbar-track {
                    background: rgba(0, 0, 0, 0.04);
                    border-radius: 10px;
                }
                .checkout-scroll::-webkit-scrollbar-thumb {
                    background: #10b981;
                    border-radius: 10px;
                }
                .checkout-scroll::-webkit-scrollbar-thumb:hover {
                    background: #059669;
                }
                .dark .checkout-scroll::-webkit-scrollbar-track {
                    background: rgba(255, 255, 255, 0.05);
                }
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: rgba(0, 0, 0, 0.04);
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #a1a1aa;
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #71717a;
                }
                .dark .custom-scrollbar::-webkit-scrollbar-track {
                    background: rgba(255, 255, 255, 0.05);
                }
            </style>

            <!-- Header -->
            <div class="sticky top-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-900/[0.04] dark:border-white/[0.06] px-6 sm:px-8 py-4 sm:py-5 flex justify-between items-center z-20">
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 rounded-full">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </span>
                    {{ __('Checkout') }}
                </h2>
                <button
                    wire:click="$set('showCheckout', false)"
                    class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                    aria-label="{{ __('Close') }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="checkout-scroll overflow-y-auto flex-1" style="scrollbar-width: thin;">
                <div class="p-6 sm:p-8 space-y-6">
                    @if(session('error'))
                        <div class="flex items-start gap-2.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 ring-1 ring-red-600/10 dark:ring-red-500/20 rounded-2xl px-4 py-3 text-sm font-medium" role="alert">
                            <svg class="w-4.5 h-4.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @include('components.public.checkouts.'.$checkoutVariant)
                </div>
            </div>
        </div>
    </div>
@endif
