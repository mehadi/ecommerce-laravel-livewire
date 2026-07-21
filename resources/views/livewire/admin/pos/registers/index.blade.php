<div class="space-y-6">
    <x-admin.page-header :heading="__('POS Registers')" :description="__('Manage the tills staff can sell from')">
        <flux:button wire:click="createRegister" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add Register') }}</span>
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
        <x-admin.stat-card :label="__('Total Registers')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 4h4m-7 4h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Active')" :value="$stats['active']" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
        <x-admin.stat-card :label="__('Open Shifts')" :value="$stats['open_shifts']" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or code...') }}" />
            </flux:field>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <x-admin.sortable-th field="name" :label="__('Name')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="code" :label="__('Code')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($registers as $register)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-zinc-900 dark:text-white">{{ $register->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-zinc-700 dark:text-zinc-300">{{ $register->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $register->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :variant="$register->is_active ? 'success' : 'danger'" size="sm">
                                {{ $register->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="editRegister({{ $register->id }})" size="sm" variant="ghost">{{ __('Edit') }}</flux:button>
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this register?') }}"
                                    wire:click="deleteRegister({{ $register->id }})" size="sm">
                                    {{ __('Delete') }}
                                </x-admin.confirm-delete-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="5" :title="__('No registers found')" :description="__('Add your first register to start selling from the POS terminal.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 4h4m-7 4h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($registers->hasPages())
        <div class="mt-4">{{ $registers->links() }}</div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="register-modal">
            <form wire:submit.prevent="saveRegister" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Register') : __('Add Register') }}</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Name') }}</flux:label>
                        <flux:input required wire:model="name" placeholder="e.g., Front Counter" />
                        <flux:error name="name" />
                    </flux:field>
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Code') }}</flux:label>
                        <flux:input required wire:model="code" placeholder="e.g., POS-1" />
                        <flux:error name="code" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Warehouse') }}</flux:label>
                    <flux:select wire:model="warehouse_id">
                        <option value="">{{ __('Select a warehouse') }}</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="warehouse_id" />
                </flux:field>

                <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Is Active') }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Inactive registers cannot be selected for a new shift.') }}</p>
                    </div>
                    <flux:switch wire:model="is_active" />
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="saveRegister">
                        <span wire:loading.remove wire:target="saveRegister">{{ $editingId ? __('Update Register') : __('Save Register') }}</span>
                        <span wire:loading wire:target="saveRegister">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
