{{--
    Shared receipt body — @include'd (not an <x-... /> component, since this
    lives alongside the Livewire view rather than under components/) by both
    the post-checkout screen and the reprint modal. Expects an `$order` with
    items/payments/customer eager-loaded.
--}}
<div class="space-y-1 pb-3 text-center text-sm">
    <div class="font-semibold">{{ \App\Models\Setting::get('site_name', config('app.name')) }}</div>
    @if ($address = \App\Models\Setting::get('contact_address'))
        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $address }}</div>
    @endif
    @if ($phone = \App\Models\Setting::get('contact_phone'))
        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $phone }}</div>
    @endif
    @if ($email = \App\Models\Setting::get('contact_email'))
        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $email }}</div>
    @endif
</div>

<div class="space-y-1 border-t border-dashed border-zinc-300 py-3 text-sm dark:border-zinc-700">
    <div class="flex justify-between"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Receipt') }}</span><span class="font-mono">{{ $order->order_number }}</span></div>
    <div class="flex justify-between"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Date') }}</span><span>{{ $order->created_at->format('Y-m-d H:i') }}</span></div>
    <div class="flex justify-between">
        <span class="text-zinc-500 dark:text-zinc-400">{{ __('Customer') }}</span>
        <span>{{ $order->customer?->name ?? $order->customer_name ?? __('Walk-in') }}</span>
    </div>
    @if ($customerPhone = $order->customer?->phone ?? $order->customer_phone)
        <div class="flex justify-between"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Phone') }}</span><span>{{ $customerPhone }}</span></div>
    @endif
</div>

<div class="space-y-1 border-t border-b border-dashed border-zinc-300 py-3 dark:border-zinc-700">
    @foreach ($order->items as $item)
        <div class="flex justify-between text-sm">
            <span>{{ $item->quantity }} &times; {{ $item->product_name }}</span>
            <span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item->subtotal, 2) }}</span>
        </div>
    @endforeach
</div>

<div class="space-y-1 py-3 text-sm">
    <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($order->subtotal, 2) }}</span></div>
    <div class="flex justify-between"><span>{{ __('Discount') }}</span><span class="tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($order->discount, 2) }}</span></div>
    <div class="flex justify-between text-lg font-semibold"><span>{{ __('Total') }}</span><span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($order->total, 2) }}</span></div>
</div>

<div class="space-y-1 border-t border-dashed border-zinc-300 pt-3 text-sm dark:border-zinc-700">
    @foreach ($order->payments as $payment)
        <div class="flex justify-between">
            <span class="capitalize">{{ str_replace('_', ' ', $payment->method) }}</span>
            <span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($payment->amount, 2) }}</span>
        </div>
        @if ($payment->change_given > 0)
            <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                <span>{{ __('Change') }}</span>
                <span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($payment->change_given, 2) }}</span>
            </div>
        @endif
    @endforeach
</div>

@if ($order->notes)
    <div class="border-t border-dashed border-zinc-300 pt-3 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
        {{ $order->notes }}
    </div>
@endif
