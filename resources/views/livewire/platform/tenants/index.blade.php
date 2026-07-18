<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Tenants') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage every store\'s subscription and billing') }}
            </flux:text>
        </div>
        <flux:button wire:click="createTenant" variant="primary">
            {{ __('Add Tenant') }}
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Tenants') }}</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active') }}</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('On Trial') }}</p>
            <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['trial'] }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Suspended') }}</p>
            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['suspended'] }}</p>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search tenants...') }}" />
            </flux:field>
        </div>
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
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                        <div class="flex items-center gap-2">
                            {{ __('Name') }}
                            @if($sortField === 'name')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Plan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Trial Ends') }}</th>
                    <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                        <div class="flex items-center gap-2">
                            {{ __('Created') }}
                            @if($sortField === 'created_at')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</span>
                                <span class="text-xs text-zinc-400">{{ $tenant->slug }}</span>
                                @if($tenant->upgrade_requested_at)
                                    <flux:badge variant="warning" size="sm">{{ __('Upgrade requested') }}</flux:badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="info" size="sm">{{ $tenant->plan?->name ?? __('None') }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :variant="$tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'danger' : 'warning')" size="sm">
                                {{ ucfirst($tenant->status) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400 text-sm">
                            {{ $tenant->trial_ends_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400 text-sm">{{ $tenant->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button :href="route('platform.tenants.show', $tenant)" size="sm" variant="ghost" wire:navigate>
                                    {{ __('Manage') }}
                                </flux:button>
                                @if($tenant->status === 'cancelled')
                                    <flux:button wire:click="deleteTenant({{ $tenant->id }})" size="sm" variant="danger"
                                        wire:confirm="{{ __('Permanently delete this tenant and ALL of its data (products, orders, users, domains)? This cannot be undone.') }}">
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No tenants found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
        <div class="mt-4">
            {{ $tenants->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="tenant-modal">
            <form wire:submit.prevent="storeTenant" class="space-y-6">
                <flux:heading>{{ __('Add New Tenant') }}</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Store Name') }} *</flux:label>
                        <flux:input wire:model.live.debounce.400ms="name" placeholder="e.g., Acme Store" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Slug') }} *</flux:label>
                        <flux:input wire:model="slug" placeholder="e.g., acme-store" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Used as the subdomain: slug.yourdomain.com') }}</p>
                        <flux:error name="slug" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Owner Name') }} *</flux:label>
                    <flux:input wire:model="owner_name" placeholder="e.g., Jane Doe" />
                    <flux:error name="owner_name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Owner Email') }} *</flux:label>
                    <flux:input type="email" wire:model="owner_email" placeholder="owner@example.com" />
                    <flux:error name="owner_email" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Owner Password') }} *</flux:label>
                        <flux:input type="password" wire:model="owner_password" />
                        <flux:error name="owner_password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Confirm Password') }} *</flux:label>
                        <flux:input type="password" wire:model="owner_password_confirmation" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Plan') }}</flux:label>
                        <flux:select wire:model="plan_id">
                            <option value="">{{ __('Default plan') }}</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->priceLabel() }})</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="plan_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Status') }} *</flux:label>
                        <flux:select wire:model="status">
                            <option value="active">{{ __('Active') }}</option>
                            <option value="suspended">{{ __('Suspended') }}</option>
                            <option value="cancelled">{{ __('Cancelled') }}</option>
                        </flux:select>
                        <flux:error name="status" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Trial Ends') }}</flux:label>
                    <flux:input wire:model="trial_ends_at" type="date" />
                    <flux:error name="trial_ends_at" />
                </flux:field>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="storeTenant">{{ __('Create Tenant') }}</span>
                        <span wire:loading wire:target="storeTenant">{{ __('Creating...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
