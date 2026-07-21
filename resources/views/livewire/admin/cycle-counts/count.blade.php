<div class="space-y-6 max-w-4xl">
    <x-admin.page-header :heading="__('Cycle Count')" :description="__(':warehouse — :count item(s)', ['warehouse' => $cycleCount->warehouse->name, 'count' => $cycleCount->items->count()])">
        <flux:button :href="route('admin.cycle-counts.index')" variant="ghost" wire:navigate>
            {{ __('Back to Cycle Counts') }}
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Product') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Expected') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Counted') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Discrepancy') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($cycleCount->items as $item)
                    @php $discrepancy = $countedQuantities[$item->id] !== null && $countedQuantities[$item->id] !== '' ? ((int) $countedQuantities[$item->id] - $item->expected_quantity) : null; @endphp
                    <tr>
                        <td class="px-6 py-3 text-sm text-zinc-900 dark:text-white">
                            {{ $item->product->name_en }}
                            @if($item->productAttribute)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->productAttribute->attribute_label }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $item->expected_quantity }}</td>
                        <td class="px-6 py-3">
                            <flux:input type="number" min="0" wire:model="countedQuantities.{{ $item->id }}" class="w-28" />
                        </td>
                        <td class="px-6 py-3 text-sm">
                            @if($discrepancy === null)
                                <span class="text-zinc-400">—</span>
                            @elseif($discrepancy === 0)
                                <span class="text-emerald-600 dark:text-emerald-400">{{ __('Match') }}</span>
                            @else
                                <span class="{{ $discrepancy > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                                    {{ $discrepancy > 0 ? '+' : '' }}{{ $discrepancy }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-3">
        <flux:button wire:click="saveProgress" variant="ghost" wire:loading.attr="disabled" wire:target="saveProgress">
            {{ __('Save Progress') }}
        </flux:button>
        <flux:button wire:click="completeCount"
            wire:confirm="{{ __('Complete this count? Any discrepancies will adjust warehouse stock immediately.') }}"
            variant="primary" wire:loading.attr="disabled" wire:target="completeCount">
            {{ __('Complete Count') }}
        </flux:button>
    </div>
</div>
