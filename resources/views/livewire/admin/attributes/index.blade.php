<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Product Attributes') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage dynamic product attributes like Color, Size, Weight') }}
            </flux:text>
        </div>
        <flux:button wire:click="openCreateModal" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Create Attribute') }}</span>
            </span>
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Unit') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Values') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($allAttributes as $attribute)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $attribute->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $attribute->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge>{{ ucfirst($attribute->type) }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
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
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button wire:click="deleteAttribute({{ $attribute->id }})" wire:confirm="{{ __('Are you sure?') }}" size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('No attributes found. Create your first attribute.') }}</p>
                        </td>
                    </tr>
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
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model="name" placeholder="{{ __('e.g., Color, Size, Weight') }}" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Slug') }} *</flux:label>
                    <flux:input wire:model="slug" placeholder="{{ __('e.g., color, size, weight') }}" />
                    <flux:error name="slug" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Type') }} *</flux:label>
                        <flux:select wire:model="type">
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

                    <flux:field>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <flux:checkbox wire:model="is_active" />
                        <flux:description>{{ __('Active') }}</flux:description>
                    </flux:field>
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
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
                            <flux:label>{{ __('Value') }} *</flux:label>
                            <flux:input wire:model="valueName" placeholder="{{ __('e.g., Red, Small, 1.5') }}" />
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

                        <flux:field>
                            <flux:label>{{ __('Active') }}</flux:label>
                            <flux:checkbox wire:model="valueIsActive" />
                        </flux:field>
                    </div>

                    <flux:button type="submit" variant="primary">{{ __('Add Value') }}</flux:button>
                </form>

                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-4">{{ __('Existing Values') }}</h3>
                    <div class="space-y-2">
                        @foreach($attributeValues as $value)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                                <div>
                                    <span class="font-medium">{{ $value['value'] }}</span>
                                    @if($value['display_value'])
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $value['display_value'] }})</span>
                                    @endif
                                </div>
                                <flux:button wire:click="deleteValue({{ $value['id'] }})" wire:confirm="{{ __('Are you sure?') }}" size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-4">
                    <flux:button wire:click="closeValuesModal" variant="ghost">{{ __('Close') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
