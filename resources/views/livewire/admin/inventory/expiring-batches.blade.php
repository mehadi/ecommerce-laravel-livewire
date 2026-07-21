<div class="space-y-6">
    <x-admin.page-header :heading="__('Expiring Batches')" :description="__('Batches expiring within 30 days or already past their expiry date')">
        <flux:button :href="route('admin.inventory.index')" variant="ghost" wire:navigate>
            {{ __('Back to Inventory') }}
        </flux:button>
    </x-admin.page-header>

    <div class="flex flex-wrap gap-4 items-end">
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('Expiring Soon or Expired') }}</option>
                <option value="expiring_soon">{{ __('Expiring Soon Only') }}</option>
                <option value="expired">{{ __('Expired Only') }}</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Product') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Batch #') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Quantity') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Expires') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($batches as $batch)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-900 dark:text-white">{{ $batch->product->name_en }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-zinc-700 dark:text-zinc-300">{{ $batch->batch_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->quantity }} {{ __('units') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $batch->expires_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($batch->isExpired())
                                <flux:badge variant="danger" size="sm">{{ __('Expired') }}</flux:badge>
                            @else
                                <flux:badge variant="warning" size="sm">{{ __('Expiring Soon') }}</flux:badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('Nothing expiring')" :description="__('No batches are expired or expiring soon.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
