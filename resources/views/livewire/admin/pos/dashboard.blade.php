<div class="space-y-6">
    <x-admin.page-header :heading="__('POS Dashboard')" :description="__('Today\'s in-person sales activity')" />

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-admin.stat-card :label="__('Today\'s POS Sales')" :value="number_format($stats['today_sales_total'], 2)" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Today\'s POS Orders')" :value="$stats['today_sales_count']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 4h4m-7 4h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Active Shifts / Cash in Drawers')" :value="$stats['active_shifts'].' / '.number_format($stats['cash_in_drawers'], 2)" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 4h4m-7 4h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Today\'s Refunds')" :value="number_format($stats['today_refunds_total'], 2)" tone="red">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l-4 4m0 0l4 4m-4-4h11a4 4 0 000-8h-1"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="sm" class="mb-3">{{ __('Top Products at the Register (30 days)') }}</flux:heading>
            <div class="space-y-2">
                @forelse ($topProducts as $product)
                    <div class="flex items-center justify-between text-sm">
                        <span>{{ $product->product_name }}</span>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ $product->total_quantity }} {{ __('units') }} &middot; {{ number_format($product->total_revenue, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No POS sales in the last 30 days.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="sm" class="mb-3">{{ __('Recent POS Sales') }}</flux:heading>
            <div class="space-y-2">
                @forelse ($recentSales as $sale)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-mono">{{ $sale->order_number }}</span>
                        <span>{{ $sale->customer?->name ?? $sale->customer_name }}</span>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ number_format($sale->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No POS sales yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
