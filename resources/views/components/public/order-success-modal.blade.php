@if($showOrderConfirmation ?? false && $order ?? null)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 backdrop-blur-sm animate-fade-in motion-reduce:animate-none p-4"
        @click.self="$wire.set('showOrderConfirmation', false)"
        @keydown.escape.window="$wire.set('showOrderConfirmation', false)"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Order Confirmed!') }}"
    >
        <div
            x-data
            x-trap.noscroll="true"
            class="bg-white dark:bg-zinc-900 rounded-3xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] animate-zoom-in motion-reduce:animate-none overflow-hidden"
        >
            <!-- Header -->
            <div class="sticky top-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-900/[0.06] dark:border-white/[0.08] px-6 sm:px-8 py-4 flex justify-between items-center z-20">
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                    {{ __('Order Confirmed!') }}
                </h2>
                <button
                    wire:click="$set('showOrderConfirmation', false)"
                    class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                    aria-label="{{ __('Close') }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-1 p-6 sm:p-8 space-y-6" style="scrollbar-width: thin;">
                <!-- Success Message -->
                <div class="text-center bg-gradient-to-b from-emerald-50 to-white dark:from-emerald-900/20 dark:to-zinc-900 rounded-3xl p-8 sm:p-10 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 dark:bg-emerald-900/50 ring-8 ring-emerald-50 dark:ring-emerald-900/20 mb-5">
                        <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="font-display text-2xl font-bold text-zinc-900 dark:text-white mb-1.5">{{ __('Thank you for your order!') }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-5">{{ __('Your order has been placed successfully') }}</p>
                    <div class="inline-block bg-white dark:bg-zinc-800 rounded-2xl px-6 py-3 ring-1 ring-emerald-600/20 dark:ring-emerald-500/30 shadow-sm">
                        <p class="text-[11px] uppercase tracking-wider font-medium text-zinc-500 dark:text-zinc-400 mb-0.5">{{ __('Order Number') }}</p>
                        <p class="font-display text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $order->order_number }}</p>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                    <h4 class="text-base font-display font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08]">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </span>
                        {{ __('Order Details') }}
                    </h4>

                    <!-- Order Items -->
                    <div class="space-y-2 mb-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center bg-white dark:bg-zinc-900 rounded-2xl px-4 py-3 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm text-zinc-900 dark:text-white font-medium">{{ $item->product_name }}</span>
                                    <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-2 tabular-nums">&times;{{ $item->quantity }}</span>
                                </div>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="border-t border-zinc-900/[0.06] dark:border-white/[0.08] pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-300">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="font-medium tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-sm text-emerald-600 dark:text-emerald-400">
                                <span>{{ __('Discount') }}</span>
                                <span class="font-medium tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-2 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
                            <span class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
                            <span class="font-display text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                    <h4 class="text-base font-display font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08]">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </span>
                        {{ __('Delivery Information') }}
                    </h4>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex gap-2"><dt class="font-medium text-zinc-900 dark:text-white min-w-24">{{ __('Name') }}:</dt><dd class="text-zinc-600 dark:text-zinc-300">{{ $order->customer_name }}</dd></div>
                        <div class="flex gap-2"><dt class="font-medium text-zinc-900 dark:text-white min-w-24">{{ __('Email') }}:</dt><dd class="text-zinc-600 dark:text-zinc-300 break-all">{{ $order->customer_email }}</dd></div>
                        <div class="flex gap-2"><dt class="font-medium text-zinc-900 dark:text-white min-w-24">{{ __('Phone') }}:</dt><dd class="text-zinc-600 dark:text-zinc-300 tabular-nums">{{ $order->customer_phone }}</dd></div>
                        <div class="flex gap-2"><dt class="font-medium text-zinc-900 dark:text-white min-w-24">{{ __('Address') }}:</dt><dd class="text-zinc-600 dark:text-zinc-300">{{ $order->shipping_address }}</dd></div>
                        @if($order->shipping_city)
                            <div class="flex gap-2"><dt class="font-medium text-zinc-900 dark:text-white min-w-24">{{ __('City') }}:</dt><dd class="text-zinc-600 dark:text-zinc-300">{{ $order->shipping_city }}</dd></div>
                        @endif
                        @if($order->shipping_postal_code)
                            <div class="flex gap-2"><dt class="font-medium text-zinc-900 dark:text-white min-w-24">{{ __('Postal Code') }}:</dt><dd class="text-zinc-600 dark:text-zinc-300 tabular-nums">{{ $order->shipping_postal_code }}</dd></div>
                        @endif
                    </dl>
                </div>

                <!-- Payment Information -->
                <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
                    <h4 class="text-base font-display font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08]">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        {{ __('Payment Information') }}
                    </h4>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">
                        <strong class="font-medium text-zinc-900 dark:text-white">{{ __('Payment Method') }}:</strong>
                        <span class="capitalize">{{ __('Cash on Delivery') }} (COD)</span>
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">
                        {{ __('You will pay when the order is delivered to your address.') }}
                    </p>
                </div>

                <!-- Next Steps -->
                <div class="bg-emerald-50/60 dark:bg-emerald-900/15 rounded-3xl p-6 sm:p-8 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                    <h4 class="text-base font-display font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('What\'s Next?') }}
                    </h4>
                    <ul class="space-y-2.5 text-sm text-zinc-600 dark:text-zinc-300">
                        @foreach([
                            __('You will receive an order confirmation email shortly'),
                            __('Our team will process your order and contact you soon'),
                            __('Your order will be delivered to the address provided'),
                        ] as $step)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4.5 h-4.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $step }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Action Button -->
                <button
                    wire:click="$set('showOrderConfirmation', false)"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-4 rounded-full text-base font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    {{ __('Continue Shopping') }}
                </button>
            </div>
        </div>
    </div>
@endif
