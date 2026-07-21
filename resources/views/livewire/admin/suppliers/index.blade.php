<div class="space-y-6">
    <x-admin.page-header :heading="__('Suppliers')" :description="__('Manage the vendors you purchase inventory from')">
        <flux:button wire:click="createSupplier" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Supplier') }}</span>
            </span>
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.stat-card :label="__('Total Suppliers')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21h2m0 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-4a1 1 0 011-1h0a1 1 0 011 1v4"></path>
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
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name, contact, or email...') }}" />
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Contact') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Lead Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Purchase Orders') }}</th>
                    <x-admin.sortable-th field="is_active" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $supplier->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $supplier->contact_name ?? '—' }}</div>
                            @if($supplier->email)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $supplier->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $supplier->lead_time_days !== null ? __(':days day(s)', ['days' => $supplier->lead_time_days]) : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->purchase_orders_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :variant="$supplier->is_active ? 'success' : 'danger'" size="sm">
                                {{ $supplier->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="editSupplier({{ $supplier->id }})" size="sm" variant="ghost">
                                    {{ __('Edit') }}
                                </flux:button>
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this supplier?') }}"
                                    wire:click="deleteSupplier({{ $supplier->id }})" size="sm">
                                    {{ __('Delete') }}
                                </x-admin.confirm-delete-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('No suppliers found')" :description="__('Add your first supplier to start tracking purchase orders.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21h2"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="supplier-modal">
            <form wire:submit.prevent="{{ $editingId ? 'updateSupplier' : 'storeSupplier' }}" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Supplier') : __('Add New Supplier') }}</flux:heading>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Name') }}</flux:label>
                    <flux:input required wire:model="name" placeholder="e.g., Sundarban Organics Ltd." />
                    <flux:error name="name" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Contact Name') }}</flux:label>
                        <flux:input wire:model="contact_name" />
                        <flux:error name="contact_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email') }}</flux:label>
                        <flux:input type="email" wire:model="email" />
                        <flux:error name="email" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Phone') }}</flux:label>
                        <flux:input wire:model="phone" />
                        <flux:error name="phone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Lead Time (days)') }}</flux:label>
                        <flux:input type="number" min="0" wire:model="lead_time_days" placeholder="e.g., 7" />
                        <flux:error name="lead_time_days" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Address') }}</flux:label>
                    <flux:textarea wire:model="address" rows="2" />
                    <flux:error name="address" />
                </flux:field>

                <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Is Active') }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Inactive suppliers are hidden from selectors elsewhere in the admin panel.') }}</p>
                    </div>
                    <flux:switch wire:model="is_active" />
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="storeSupplier,updateSupplier">
                        <span wire:loading.remove wire:target="storeSupplier,updateSupplier">{{ $editingId ? __('Update Supplier') : __('Save Supplier') }}</span>
                        <span wire:loading wire:target="storeSupplier,updateSupplier">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
