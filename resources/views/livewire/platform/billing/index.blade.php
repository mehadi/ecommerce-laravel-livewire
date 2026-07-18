<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Billing') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Billing events across every tenant') }}
            </flux:text>
        </div>
        <flux:button wire:click="export" variant="ghost">
            {{ __('Export CSV') }}
        </flux:button>
    </div>

    {{-- Summary --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Recorded Revenue') }}</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        @foreach(['plan_changed' => __('Plan Changes'), 'payment_recorded' => __('Payments'), 'suspended' => __('Suspensions')] as $key => $label)
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $summary['by_type'][$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-4 items-end">
        <flux:field>
            <flux:label>{{ __('Type') }}</flux:label>
            <flux:select wire:model.live="type">
                <option value="">{{ __('All Types') }}</option>
                <option value="plan_changed">{{ __('Plan Changed') }}</option>
                <option value="status_changed">{{ __('Status Changed') }}</option>
                <option value="payment_recorded">{{ __('Payment Recorded') }}</option>
                <option value="upgrade_approved">{{ __('Upgrade Approved') }}</option>
                <option value="upgrade_rejected">{{ __('Upgrade Rejected') }}</option>
                <option value="suspended">{{ __('Suspended') }}</option>
                <option value="reactivated">{{ __('Reactivated') }}</option>
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Tenant') }}</flux:label>
            <flux:select wire:model.live="tenantId">
                <option value="">{{ __('All Tenants') }}</option>
                @foreach($tenants as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>{{ __('From') }}</flux:label>
            <flux:input type="date" wire:model.live="from" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('To') }}</flux:label>
            <flux:input type="date" wire:model.live="to" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Per Page') }}</flux:label>
            <flux:select wire:model.live="perPage">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                        {{ __('Date') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tenant') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Note') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Recorded By') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($events as $event)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $event->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:button :href="route('platform.tenants.show', $event->tenant)" size="sm" variant="ghost" wire:navigate>
                                {{ $event->tenant?->name ?? __('Unknown') }}
                            </flux:button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="subtle" size="sm">{{ str_replace('_', ' ', $event->type) }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                            {{ $event->amount !== null ? number_format($event->amount, 2) : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $event->note }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $event->recordedBy?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No billing events found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @endif
</div>
