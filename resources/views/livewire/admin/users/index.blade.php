<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Users') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Manage system users and their roles') }}
            </flux:text>
        </div>
        @can('create users')
            <flux:button wire:click="openModal" variant="primary">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('Add New User') }}</span>
                </span>
            </flux:button>
        @endcan
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
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Users') }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Verified') }}</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['verified'] }}</p>
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Unverified') }}</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['unverified'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:bg-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('With Roles') }}</p>
                    <p class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['with_roles'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterRole">
                <option value="">{{ __('All Roles') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
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
                    <th wire:click="sortBy('email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                        <div class="flex items-center gap-2">
                            {{ __('Email') }}
                            @if($sortField === 'email')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Roles') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
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
                @forelse($users as $user)
                    @php
                        $isSuperAdmin = $user->hasRole('super admin');
                        $isCurrentUser = $user->id === auth()->id();
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-sm font-medium">
                                    {{ $user->initials() }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    @if($isCurrentUser)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('You') }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-900 dark:text-white">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <flux:badge 
                                        :variant="$role->name === 'super admin' ? 'warning' : ($role->name === 'admin' ? 'info' : 'subtle')" 
                                        size="sm">
                                        {{ ucfirst($role->name) }}
                                    </flux:badge>
                                @empty
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('No roles') }}</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($user->email_verified_at)
                                    <flux:badge variant="success" size="sm">
                                        {{ __('Verified') }}
                                    </flux:badge>
                                @else
                                    <flux:badge variant="danger" size="sm">
                                        {{ __('Unverified') }}
                                    </flux:badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400 text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                @can('edit users')
                                    @if(!$isSuperAdmin || auth()->user()->hasRole('super admin'))
                                        <flux:button wire:click="openModal({{ $user->id }})" size="sm" variant="ghost">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                <span>{{ __('Edit') }}</span>
                                            </span>
                                        </flux:button>
                                    @endif
                                @endcan
                                @can('delete users')
                                    @if(!$isSuperAdmin && !$isCurrentUser)
                                        <flux:button wire:click="delete({{ $user->id }})" size="sm" variant="danger" 
                                            wire:confirm="{{ __('Are you sure you want to delete this user?') }}">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <span>{{ __('Delete') }}</span>
                                            </span>
                                        </flux:button>
                                    @else
                                        <flux:button disabled size="sm" variant="ghost" class="opacity-50 cursor-not-allowed">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                                <span>{{ __('Protected') }}</span>
                                            </span>
                                        </flux:button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('No users found') }}</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Get started by creating your first user.') }}
                                    </p>
                                </div>
                                @can('create users')
                                    <flux:button wire:click="openModal" variant="primary" size="sm">
                                        {{ __('Add New User') }}
                                    </flux:button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="user-modal">
            <form wire:submit.prevent="save" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit User') : __('Create User') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model="name" placeholder="{{ __('Full name') }}" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email') }} *</flux:label>
                    <flux:input type="email" wire:model="email" placeholder="{{ __('user@example.com') }}" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        {{ __('Password') }}
                        @if($editingId)
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ __('Leave blank to keep current password') }})</span>
                        @else
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">*</span>
                        @endif
                    </flux:label>
                    <flux:input type="password" wire:model="password" placeholder="{{ __('Enter password') }}" />
                    <flux:error name="password" />
                </flux:field>

                @if(!$editingId)
                    <flux:field>
                        <flux:label>{{ __('Confirm Password') }} *</flux:label>
                        <flux:input type="password" wire:model="password_confirmation" placeholder="{{ __('Confirm password') }}" />
                        <flux:error name="password_confirmation" />
                    </flux:field>
                @elseif(!empty($password))
                    <flux:field>
                        <flux:label>{{ __('Confirm Password') }} *</flux:label>
                        <flux:input type="password" wire:model="password_confirmation" placeholder="{{ __('Confirm password') }}" />
                        <flux:error name="password_confirmation" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ __('Roles') }}</flux:label>
                    <div class="space-y-2 max-h-[300px] overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                        @forelse($roles as $role)
                            <div class="flex items-center" wire:key="role-{{ $role->id }}">
                                <flux:checkbox 
                                    wire:model.live="selectedRoles" 
                                    value="{{ (string) $role->id }}"
                                    label="{{ ucfirst($role->name) }}"
                                    class="flex-1"
                                />
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No roles available. Create roles first.') }}</p>
                        @endforelse
                    </div>
                    <flux:error name="selectedRoles" />
                </flux:field>

                <div class="flex gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? __('Update User') : __('Create User') }}
                        </span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
