<div class="space-y-6">
    <x-admin.page-header :heading="__('Product Attributes')" :description="__('Manage dynamic product attributes like Color, Size, Weight')">
        <flux:button wire:click="openCreateModal" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Create Attribute') }}</span>
            </span>
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.stat-card :label="__('Total Attributes')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
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

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or slug...') }}" />
            </flux:field>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Unit') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Values') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($allAttributes as $attribute)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $attribute->name }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $attribute->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge>{{ ucfirst($attribute->type) }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $attribute->unit ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:button wire:click="openValuesModal({{ $attribute->id }})" size="sm" variant="ghost">
                                {{ $attribute->values()->count() }} {{ __('values') }}
                            </flux:button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :variant="$attribute->is_active ? 'success' : 'subtle'">
                                {{ $attribute->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <flux:button wire:click="openEditModal({{ $attribute->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this attribute? Its values will also be removed.') }}"
                                    wire:click="deleteAttribute({{ $attribute->id }})" size="sm">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>{{ __('Delete') }}</span>
                                    </span>
                                </x-admin.confirm-delete-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('No attributes found')" :description="__('Get started by creating your first attribute.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 11V6a3 3 0 013-3z"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button wire:click="openCreateModal" variant="primary" size="sm">{{ __('Create Attribute') }}</flux:button>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($allAttributes->hasPages())
        <div class="mt-4">
            {{ $allAttributes->links() }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if($showCreateModal || $showEditModal)
        <flux:modal wire:model="{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}" name="attribute-modal">
            <form wire:submit.prevent="saveAttribute" class="space-y-6">
                <flux:heading>{{ $editingAttribute ? __('Edit Attribute') : __('Create Attribute') }}</flux:heading>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Name') }}</flux:label>
                    <flux:input required wire:model="name" placeholder="{{ __('e.g., Color, Size, Weight') }}" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Slug') }}</flux:label>
                    <flux:input required wire:model="slug" placeholder="{{ __('e.g., color, size, weight') }}" />
                    <flux:error name="slug" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Type') }}</flux:label>
                        <flux:select required wire:model="type">
                            <option value="text">{{ __('Text') }}</option>
                            <option value="number">{{ __('Number') }}</option>
                            <option value="decimal">{{ __('Decimal') }}</option>
                        </flux:select>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Unit') }}</flux:label>
                        <flux:input wire:model="unit" placeholder="{{ __('e.g., kg, g, ml') }}" />
                        <flux:error name="unit" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Order') }}</flux:label>
                        <flux:input type="number" wire:model="order" min="0" />
                        <flux:error name="order" />
                    </flux:field>

                    <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Active') }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Visible for use on products') }}</p>
                        </div>
                        <flux:switch wire:model="is_active" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="saveAttribute">
                        <span wire:loading.remove wire:target="saveAttribute">{{ __('Save') }}</span>
                        <span wire:loading wire:target="saveAttribute">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    {{-- Values Modal --}}
    @if($showValuesModal && $selectedAttribute)
        <flux:modal wire:model="showValuesModal" name="values-modal">
            <div class="space-y-6">
                <flux:heading>{{ __('Manage Values for') }}: {{ $selectedAttribute->name }}</flux:heading>

                <form wire:submit.prevent="addValue" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label badge="{{ __('Required') }}">{{ __('Value') }}</flux:label>
                            <flux:input required wire:model="valueName" placeholder="{{ __('e.g., Red, Small, 1.5') }}" />
                            <flux:error name="valueName" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Display Value') }}</flux:label>
                            <flux:input wire:model="valueDisplay" placeholder="{{ __('e.g., 1.5 kg') }}" />
                            <flux:error name="valueDisplay" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Order') }}</flux:label>
                            <flux:input type="number" wire:model="valueOrder" min="0" />
                            <flux:error name="valueOrder" />
                        </flux:field>

                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Active') }}</p>
                            <flux:switch wire:model="valueIsActive" />
                        </div>
                    </div>

                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="addValue">
                        <span wire:loading.remove wire:target="addValue">{{ __('Add Value') }}</span>
                        <span wire:loading wire:target="addValue">{{ __('Adding...') }}</span>
                    </flux:button>
                </form>

                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <flux:heading size="md" level="3" class="mb-4">{{ __('Existing Values') }}</flux:heading>
                    <div class="space-y-2">
                        @forelse($attributeValues as $value)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $value['value'] }}</span>
                                    @if($value['display_value'])
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">({{ $value['display_value'] }})</span>
                                    @endif
                                    <flux:badge size="sm" :variant="$value['is_active'] ? 'success' : 'subtle'">
                                        {{ $value['is_active'] ? __('Active') : __('Inactive') }}
                                    </flux:badge>
                                </div>
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this value?') }}"
                                    wire:click="deleteValue({{ $value['id'] }})" size="sm">
                                    {{ __('Delete') }}
                                </x-admin.confirm-delete-button>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No values yet. Add one above.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex gap-4">
                    <flux:button wire:click="closeValuesModal" variant="ghost">{{ __('Close') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
