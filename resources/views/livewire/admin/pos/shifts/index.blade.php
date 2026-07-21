<div class="space-y-6">
    <x-admin.page-header :heading="__('POS Shifts')" :description="__('Every cash-drawer session across all registers')" />

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif
    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.stat-card :label="__('Open Shifts')" :value="$stats['open']" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Shifts Today')" :value="$stats['today']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Variance Today')" :value="number_format($stats['variance_today'], 2)" :tone="$stats['variance_today'] == 0 ? 'emerald' : 'red'">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by register or cashier...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="open">{{ __('Open') }}</option>
                <option value="closed">{{ __('Closed') }}</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Register') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Cashier') }}</th>
                    <x-admin.sortable-th field="opened_at" :label="__('Opened')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Variance') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($shifts as $shift)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-zinc-900 dark:text-white">{{ $shift->register->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $shift->openedBy->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $shift->opened_at->format('M j, Y g:i A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :variant="$shift->status === 'open' ? 'success' : 'subtle'" size="sm">
                                {{ $shift->status === 'open' ? __('Open') : __('Closed') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($shift->variance !== null)
                                <span class="{{ $shift->variance == 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($shift->variance, 2) }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($shift->status === 'open')
                                @can('force close pos shift')
                                    <x-admin.confirm-delete-button
                                        message="{{ __('Force-close this shift using the computed expected cash (no physical count)?') }}"
                                        wire:click="forceCloseShift({{ $shift->id }})" size="sm" variant="ghost">
                                        {{ __('Force Close') }}
                                    </x-admin.confirm-delete-button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('No shifts found')" :description="__('Shifts appear here once a cashier opens the POS terminal.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($shifts->hasPages())
        <div class="mt-4">{{ $shifts->links() }}</div>
    @endif
</div>
