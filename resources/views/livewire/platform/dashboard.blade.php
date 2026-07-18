<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Platform Dashboard') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Overview of all tenants on the platform') }}
            </flux:text>
        </div>
        <flux:button :href="route('platform.tenants.index')" variant="primary" wire:navigate>
            {{ __('Manage Tenants') }}
        </flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Tenants') }}</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active') }}</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('On Trial') }}</p>
            <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['trial'] }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Suspended') }}</p>
            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['suspended'] }}</p>
        </div>
        <a href="{{ route('platform.upgrade-requests.index') }}" wire:navigate class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 hover:border-amber-300 dark:hover:border-amber-700 transition-colors">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Upgrade Requests') }}</p>
            <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['upgrade_requests'] }}</p>
        </a>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading size="md" level="3">{{ __('Recent Billing Activity') }}</flux:heading>
        </div>
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($recentEvents as $event)
                <div class="px-6 py-3 flex items-center justify-between gap-4">
                    <div>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $event->tenant?->name }}</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">— {{ str_replace('_', ' ', $event->type) }}</span>
                        @if($event->amount !== null)
                            <span class="text-sm text-emerald-600 dark:text-emerald-400">({{ number_format($event->amount, 2) }})</span>
                        @endif
                    </div>
                    <span class="text-xs text-zinc-400">{{ $event->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('No billing activity yet.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
