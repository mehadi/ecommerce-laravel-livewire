<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Plans') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage subscription plans, limits, and feature flags') }}
            </flux:text>
        </div>
        <flux:button wire:click="createPlan" variant="primary">
            {{ __('Add New Plan') }}
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Order') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Price') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Limits') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Features') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tenants') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Default') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($plans as $plan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <button wire:click="moveUp({{ $plan->id }})" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" title="{{ __('Move up') }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                </button>
                                <button wire:click="moveDown({{ $plan->id }})" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" title="{{ __('Move down') }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $plan->name }}</div>
                            <div class="text-xs text-zinc-400">{{ $plan->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $plan->priceLabel() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div>{{ __('Products') }}: {{ $plan->max_products ?? __('Unlimited') }}</div>
                            <div>{{ __('Admin Users') }}: {{ $plan->max_admin_users ?? __('Unlimited') }}</div>
                            <div>{{ __('Domains') }}: {{ $plan->max_custom_domains ?? __('Unlimited') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @forelse($plan->features ?? [] as $feature)
                                    <flux:badge variant="subtle" size="sm">{{ $featureRegistry[$feature] ?? $feature }}</flux:badge>
                                @empty
                                    <span class="text-xs text-zinc-400">{{ __('None') }}</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $plan->tenants_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($plan->is_default)
                                <flux:badge variant="success" size="sm">{{ __('Default') }}</flux:badge>
                            @else
                                <flux:button wire:click="setDefault({{ $plan->id }})" size="sm" variant="ghost">{{ __('Make Default') }}</flux:button>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="editPlan({{ $plan->id }})" size="sm" variant="ghost">{{ __('Edit') }}</flux:button>
                                <flux:button wire:click="deletePlan({{ $plan->id }})" size="sm" variant="danger"
                                    wire:confirm="{{ __('Are you sure you want to delete this plan?') }}">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No plans found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
        <flux:modal wire:model="showModal" name="plan-modal">
            <form wire:submit.prevent="{{ $editingId ? 'updatePlan' : 'storePlan' }}" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Plan') : __('Add New Plan') }}</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Name') }} *</flux:label>
                        <flux:input wire:model="name" placeholder="e.g., Growth" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Slug') }} *</flux:label>
                        <flux:input wire:model="slug" placeholder="e.g., growth" />
                        <flux:error name="slug" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Price') }} *</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model="price" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Informational only — billing is manual.') }}</p>
                        <flux:error name="price" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Billing period') }} *</flux:label>
                        <flux:radio.group wire:model="billing_period" variant="segmented">
                            <flux:radio value="monthly" label="{{ __('Monthly') }}" />
                            <flux:radio value="yearly" label="{{ __('Yearly') }}" />
                        </flux:radio.group>
                        <flux:error name="billing_period" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Max Products') }}</flux:label>
                        <flux:input type="number" min="0" wire:model="max_products" placeholder="{{ __('Unlimited') }}" />
                        <flux:error name="max_products" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Max Admin Users') }}</flux:label>
                        <flux:input type="number" min="0" wire:model="max_admin_users" placeholder="{{ __('Unlimited') }}" />
                        <flux:error name="max_admin_users" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Max Custom Domains') }}</flux:label>
                        <flux:input type="number" min="0" wire:model="max_custom_domains" placeholder="{{ __('Unlimited') }}" />
                        <flux:error name="max_custom_domains" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Features') }}</flux:label>
                    <div class="space-y-2">
                        @foreach($featureRegistry as $key => $label)
                            <flux:checkbox wire:model="features.{{ $key }}" label="{{ $label }}" />
                        @endforeach
                    </div>
                </flux:field>

                <flux:checkbox wire:model="is_default" label="{{ __('Default plan for new tenants') }}" />

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="storePlan,updatePlan">{{ $editingId ? __('Update Plan') : __('Save Plan') }}</span>
                        <span wire:loading wire:target="storePlan,updatePlan">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
