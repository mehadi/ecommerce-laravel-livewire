<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Coupons') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage discount codes and promotional offers') }}
            </flux:text>
        </div>
        <flux:button wire:click="createCoupon" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Coupon') }}</span>
            </span>
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Coupons') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6m-6 0H8m6 0v3m-6-3V8m0 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2H8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active') }}</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Inactive') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-500 dark:text-zinc-400">{{ $stats['inactive'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                    <svg class="h-6 w-6 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Expired') }}</p>
                    <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/20">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Expiring Soon') }}</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['expiring_soon'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
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
        <div class="flex items-center justify-between gap-4 rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                    {{ __(':count item(s) selected', ['count' => count($selectedItems)]) }}
                </span>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="bulkToggleStatus" size="sm" variant="ghost">
                    {{ __('Toggle Status') }}
                </flux:button>
                <flux:button wire:click="bulkDelete" 
                    wire:confirm="{{ __('Are you sure you want to delete the selected coupons?') }}"
                    size="sm" variant="danger">
                    {{ __('Delete Selected') }}
                </flux:button>
            </div>
        </div>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('code')">
                        <div class="flex items-center gap-2">
                            {{ __('Code') }}
                            @if($sortField === 'code')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('name')">
                        <div class="flex items-center gap-2">
                            {{ __('Name') }}
                            @if($sortField === 'name')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('type')">
                        <div class="flex items-center gap-2">
                            {{ __('Type') }}
                            @if($sortField === 'type')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('value')">
                        <div class="flex items-center gap-2">
                            {{ __('Value') }}
                            @if($sortField === 'value')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Min. Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('used_count')">
                        <div class="flex items-center gap-2">
                            {{ __('Usage') }}
                            @if($sortField === 'used_count')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('expires_at')">
                        <div class="flex items-center gap-2">
                            {{ __('Expires At') }}
                            @if($sortField === 'expires_at')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Validity') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        wire:click="sortBy('is_active')">
                        <div class="flex items-center gap-2">
                            {{ __('Status') }}
                            @if($sortField === 'is_active')
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
                @forelse($coupons as $coupon)
                    @php
                        $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                        $isExpiringSoon = $coupon->expires_at && $coupon->expires_at->isFuture() && $coupon->expires_at->diffInDays(now()) <= 7;
                        $isUsageExceeded = $coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit;
                        $isValid = $coupon->isValid();
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selectedItems" value="{{ $coupon->id }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-gray-900 dark:text-white font-semibold">{{ $coupon->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-900 dark:text-white">{{ $coupon->name }}</div>
                            @if($coupon->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ \Illuminate\Support\Str::limit($coupon->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="subtle" size="sm">
                                {{ ucfirst($coupon->type) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900 dark:text-white font-semibold">
                                {{ $coupon->type === 'percentage' ? number_format($coupon->value, 0).'%' : \App\Models\Setting::get('currency_symbol', '৳').number_format($coupon->value, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                            @if($coupon->minimum_amount)
                                {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($coupon->minimum_amount, 2) }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">{{ __('None') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-900 dark:text-white font-medium">
                                    {{ $coupon->used_count }}
                                </span>
                                <span class="text-gray-400 dark:text-gray-500">/</span>
                                <span class="text-gray-600 dark:text-gray-400">
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
                                    <span class="text-gray-900 dark:text-white text-sm">{{ $coupon->expires_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $coupon->expires_at->format('h:i A') }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">{{ __('Never') }}</span>
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
                                    title="{{ $coupon->is_active ? __('Deactivate') : __('Activate') }}">
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
                                <flux:button wire:click="deleteCoupon({{ $coupon->id }})" size="sm" variant="danger" 
                                    wire:confirm="{{ __('Are you sure you want to delete this coupon?') }}">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>{{ __('Delete') }}</span>
                                    </span>
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6m-6 0H8m6 0v3m-6-3V8m0 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2H8z"></path>
                                </svg>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('No coupons found') }}</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Get started by creating your first coupon.') }}
                                    </p>
                                </div>
                                <flux:button wire:click="createCoupon" variant="primary" size="sm">
                                    {{ __('Add New Coupon') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
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
                    <flux:label>{{ __('Code') }} *</flux:label>
                    <flux:input wire:model="code" placeholder="e.g., SAVE20, WELCOME10" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Unique coupon code (will be converted to uppercase)') }}</p>
                    <flux:error name="code" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., Summer Sale 2024" />
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
                        <flux:label>{{ __('Type') }} *</flux:label>
                        <flux:select wire:model="type">
                            <option value="percentage">{{ __('Percentage') }}</option>
                            <option value="fixed">{{ __('Fixed Amount') }}</option>
                        </flux:select>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Discount type') }}</p>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Value') }} *</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model="value" />
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

                <flux:checkbox wire:model="is_active" label="{{ __('Is Active') }}" />
                <p class="text-xs text-zinc-500 dark:text-zinc-400 -mt-4">{{ __('Inactive coupons cannot be used by customers') }}</p>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="storeCoupon,updateCoupon">{{ $editingId ? __('Update Coupon') : __('Save Coupon') }}</span>
                        <span wire:loading wire:target="storeCoupon,updateCoupon">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
