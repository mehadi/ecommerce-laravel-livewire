<div class="space-y-6">
    <x-admin.page-header :heading="__('Coupons')" :description="__('Manage discount codes and promotional offers')">
        <flux:button wire:click="createCoupon" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Coupon') }}</span>
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
    <div class="grid gap-4 md:grid-cols-5">
        <x-admin.stat-card :label="__('Total Coupons')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6m-6 0H8m6 0v3m-6-3V8m0 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2H8z"></path>
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

        <x-admin.stat-card :label="__('Expired')" :value="$stats['expired']" tone="red">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Expiring Soon')" :value="$stats['expiring_soon']" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by code, name, or description...') }}" />
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
            <flux:select wire:model.live="filterType">
                <option value="">{{ __('All Types') }}</option>
                <option value="percentage">{{ __('Percentage') }}</option>
                <option value="fixed">{{ __('Fixed Amount') }}</option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterValidity">
                <option value="">{{ __('All Validity') }}</option>
                <option value="valid">{{ __('Valid') }}</option>
                <option value="expired">{{ __('Expired') }}</option>
                <option value="expiring_soon">{{ __('Expiring Soon') }}</option>
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

    {{-- Bulk Actions --}}
    @if(count($selectedItems) > 0)
        <x-admin.bulk-actions-bar :count="count($selectedItems)">
            <flux:button wire:click="bulkToggleStatus" size="sm" variant="ghost" wire:loading.attr="disabled" wire:target="bulkToggleStatus">
                {{ __('Toggle Status') }}
            </flux:button>
            <flux:button wire:click="bulkDelete"
                wire:confirm="{{ __('Are you sure you want to delete the selected coupons?') }}"
                size="sm" variant="danger" wire:loading.attr="disabled" wire:target="bulkDelete">
                {{ __('Delete Selected') }}
            </flux:button>
        </x-admin.bulk-actions-bar>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" aria-label="{{ __('Select all coupons') }}" />
                    </th>
                    <x-admin.sortable-th field="code" :label="__('Code')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="name" :label="__('Name')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="type" :label="__('Type')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="value" :label="__('Value')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Min. Amount') }}</th>
                    <x-admin.sortable-th field="used_count" :label="__('Usage')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <x-admin.sortable-th field="expires_at" :label="__('Expires At')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Validity') }}</th>
                    <x-admin.sortable-th field="is_active" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($coupons as $coupon)
                    @php
                        $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                        $isExpiringSoon = $coupon->expires_at && $coupon->expires_at->isFuture() && $coupon->expires_at->diffInDays(now()) <= 7;
                        $isUsageExceeded = $coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit;
                        $isValid = $coupon->isValid();
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $coupon->id }}" aria-label="{{ __('Select :name', ['name' => $coupon->name]) }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-zinc-900 dark:text-white font-semibold">{{ $coupon->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-zinc-900 dark:text-white">{{ $coupon->name }}</div>
                            @if($coupon->description)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-1">{{ \Illuminate\Support\Str::limit($coupon->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="subtle" size="sm">
                                {{ ucfirst($coupon->type) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-zinc-900 dark:text-white font-semibold">
                                {{ $coupon->type === 'percentage' ? number_format($coupon->value, 0).'%' : \App\Models\Setting::get('currency_symbol', '৳').number_format($coupon->value, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-900 dark:text-white">
                            @if($coupon->minimum_amount)
                                {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($coupon->minimum_amount, 2) }}
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">{{ __('None') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-900 dark:text-white font-medium">
                                    {{ $coupon->used_count }}
                                </span>
                                <span class="text-zinc-400 dark:text-zinc-500">/</span>
                                <span class="text-zinc-600 dark:text-zinc-400">
                                    {{ $coupon->usage_limit ?? '∞' }}
                                </span>
                                @if($isUsageExceeded)
                                    <flux:badge variant="danger" size="sm">{{ __('Exceeded') }}</flux:badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($coupon->expires_at)
                                <div class="flex flex-col">
                                    <span class="text-zinc-900 dark:text-white text-sm">{{ $coupon->expires_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $coupon->expires_at->format('h:i A') }}</span>
                                </div>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">{{ __('Never') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isValid)
                                <flux:badge variant="success" size="sm">{{ __('Valid') }}</flux:badge>
                            @elseif($isExpired || $isUsageExceeded)
                                <flux:badge variant="danger" size="sm">{{ __('Expired') }}</flux:badge>
                            @elseif($isExpiringSoon)
                                <flux:badge variant="warning" size="sm">{{ __('Expiring Soon') }}</flux:badge>
                            @else
                                <flux:badge variant="subtle" size="sm">{{ __('Invalid') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:badge :variant="$coupon->is_active ? 'success' : 'danger'">
                                    {{ $coupon->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                                <button wire:click="toggleStatus({{ $coupon->id }})"
                                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                    title="{{ $coupon->is_active ? __('Deactivate') : __('Activate') }}"
                                    aria-label="{{ $coupon->is_active ? __('Deactivate :name', ['name' => $coupon->name]) : __('Activate :name', ['name' => $coupon->name]) }}">
                                    @if($coupon->is_active)
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="editCoupon({{ $coupon->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                <flux:button wire:click="duplicate({{ $coupon->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ __('Duplicate') }}</span>
                                    </span>
                                </flux:button>
                                <x-admin.confirm-delete-button
                                    message="{{ __('Are you sure you want to delete this coupon?') }}"
                                    wire:click="deleteCoupon({{ $coupon->id }})" size="sm">
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
                    <x-admin.table-empty-state colspan="11" :title="__('No coupons found')" :description="__('Get started by creating your first coupon.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6m-6 0H8m6 0v3m-6-3V8m0 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2H8z"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button wire:click="createCoupon" variant="primary" size="sm">
                            {{ __('Add New Coupon') }}
                        </flux:button>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons->hasPages())
        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="coupon-modal">
            <form wire:submit.prevent="{{ $editingId ? 'updateCoupon' : 'storeCoupon' }}" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Coupon') : __('Add New Coupon') }}</flux:heading>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Code') }}</flux:label>
                    <flux:input required wire:model="code" placeholder="e.g., SAVE20, WELCOME10" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Unique coupon code (will be converted to uppercase)') }}</p>
                    <flux:error name="code" />
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Name') }}</flux:label>
                    <flux:input required wire:model="name" placeholder="e.g., Summer Sale 2024" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Internal name for this coupon') }}</p>
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="description" rows="3" placeholder="{{ __('Optional description of this coupon offer') }}" />
                    <flux:error name="description" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Type') }}</flux:label>
                        <flux:select required wire:model="type">
                            <option value="percentage">{{ __('Percentage') }}</option>
                            <option value="fixed">{{ __('Fixed Amount') }}</option>
                        </flux:select>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Discount type') }}</p>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Value') }}</flux:label>
                        <flux:input required type="number" step="0.01" min="0" wire:model="value" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $type === 'percentage' ? __('Discount percentage (e.g., 20 for 20%)') : __('Fixed discount amount in :currency', ['currency' => \App\Models\Setting::get('currency_symbol', '৳')]) }}
                        </p>
                        <flux:error name="value" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Minimum Amount') }}</flux:label>
                    <flux:input type="number" step="0.01" min="0" wire:model="minimum_amount" placeholder="0.00" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Minimum order amount required to use this coupon (leave empty for no minimum)') }}</p>
                    <flux:error name="minimum_amount" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Usage Limit') }}</flux:label>
                    <flux:input type="number" min="1" wire:model="usage_limit" placeholder="e.g., 100" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Maximum number of times this coupon can be used (leave empty for unlimited)') }}</p>
                    <flux:error name="usage_limit" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Starts At') }}</flux:label>
                        <flux:input type="datetime-local" wire:model="starts_at" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('When the coupon becomes valid (optional)') }}</p>
                        <flux:error name="starts_at" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Expires At') }}</flux:label>
                        <flux:input type="datetime-local" wire:model="expires_at" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('When the coupon expires (optional)') }}</p>
                        <flux:error name="expires_at" />
                    </flux:field>
                </div>

                <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Is Active') }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Inactive coupons cannot be used by customers') }}</p>
                    </div>
                    <flux:switch wire:model="is_active" />
                </div>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="storeCoupon,updateCoupon">
                        <span wire:loading.remove wire:target="storeCoupon,updateCoupon">{{ $editingId ? __('Update Coupon') : __('Save Coupon') }}</span>
                        <span wire:loading wire:target="storeCoupon,updateCoupon">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
