<div class="space-y-6">
    <x-admin.page-header :heading="__('Warehouses')" :description="__('Manage the physical locations stock is tracked across')">
        @can('create warehouses')
            <flux:button wire:click="createWarehouse" variant="primary">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('Add New Warehouse') }}</span>
                </span>
            </flux:button>
        @endcan
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.stat-card :label="__('Total Warehouses')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Active')" :value="$stats['active']" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Inactive')" :value="$stats['inactive']" tone="zinc">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name, code, or city...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="perPage">
                <option value="10">10 {{ __('per page') }}</option>
                <option value="15">15 {{ __('per page') }}</option>
                <option value="25">25 {{ __('per page') }}</option>
                <option value="50">50 {{ __('per page') }}</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <x-admin.sortable-th field="name" :label="__('Name')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="code" :label="__('Code')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('City') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Stock Rows') }}</th>
                    <x-admin.sortable-th field="is_active" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($warehouses as $warehouse)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $warehouse->name }}</div>
                            @if($warehouse->is_default)
                                <flux:badge size="sm" variant="subtle" class="mt-1">{{ __('Default') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-zinc-700 dark:text-zinc-300">{{ $warehouse->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $warehouse->city ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $warehouse->warehouse_stocks_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :variant="$warehouse->is_active ? 'success' : 'danger'" size="sm">
                                {{ $warehouse->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @can('edit warehouses')
                                    @if(! $warehouse->is_default)
                                        <flux:button wire:click="setDefault({{ $warehouse->id }})" size="sm" variant="ghost">
                                            {{ __('Set Default') }}
                                        </flux:button>
                                    @endif
                                    <flux:button wire:click="editWarehouse({{ $warehouse->id }})" size="sm" variant="ghost">
                                        {{ __('Edit') }}
                                    </flux:button>
                                @endcan
                                @can('delete warehouses')
                                    <x-admin.confirm-delete-button
                                        message="{{ __('Are you sure you want to delete this warehouse?') }}"
                                        wire:click="deleteWarehouse({{ $warehouse->id }})" size="sm">
                                        {{ __('Delete') }}
                                    </x-admin.confirm-delete-button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('No warehouses found')" :description="__('Add your first warehouse to start tracking location-specific stock.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($warehouses->hasPages())
        <div class="mt-4">
            {{ $warehouses->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="warehouse-modal">
            <form wire:submit.prevent="{{ $editingId ? 'updateWarehouse' : 'storeWarehouse' }}" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Warehouse') : __('Add New Warehouse') }}</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Name') }}</flux:label>
                        <flux:input required wire:model="name" placeholder="e.g., Main Warehouse" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Code') }}</flux:label>
                        <flux:input required wire:model="code" placeholder="e.g., MAIN" />
                        <flux:error name="code" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Address') }}</flux:label>
                    <flux:textarea wire:model="address" rows="2" />
                    <flux:error name="address" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('City') }}</flux:label>
                        <flux:input wire:model="city" />
                        <flux:error name="city" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Phone') }}</flux:label>
                        <flux:input wire:model="phone" />
                        <flux:error name="phone" />
                    </flux:field>
                </div>

                <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Is Active') }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Inactive warehouses are hidden from selectors elsewhere in the admin panel.') }}</p>
                    </div>
                    <flux:switch wire:model="is_active" />
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="storeWarehouse,updateWarehouse">
                        <span wire:loading.remove wire:target="storeWarehouse,updateWarehouse">{{ $editingId ? __('Update Warehouse') : __('Save Warehouse') }}</span>
                        <span wire:loading wire:target="storeWarehouse,updateWarehouse">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
