<div class="space-y-6">
    <x-admin.page-header :heading="__('Roles')" :description="__('Manage user roles and permissions')">
        <flux:button wire:click="createRole" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('Add New Role') }}</span>
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
    <div class="grid gap-4 md:grid-cols-4">
        <x-admin.stat-card :label="__('Total Roles')" :value="$stats['total']" tone="blue">
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Total Permissions')" :value="$stats['total_permissions']" tone="purple">
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Total Users')" :value="$stats['total_users']" tone="emerald">
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Active Roles')" :value="$stats['roles_with_users']" tone="amber">
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    {{-- Filters and Search --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search roles...') }}" />
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
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <x-admin.sortable-th field="name" :label="__('Name')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Permissions') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Users') }}</th>
                    <x-admin.sortable-th field="created_at" :label="__('Created')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($roles as $role)
                    @php
                        $isProtectedRole = in_array($role->name, \App\Livewire\Admin\Roles\Index::PROTECTED_ROLE_NAMES, true);
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-zinc-900 dark:text-white">{{ ucfirst($role->name) }}</span>
                                @if($isProtectedRole)
                                    <flux:badge variant="warning" size="sm">{{ __('Protected') }}</flux:badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="info" size="sm">{{ $role->permissions_count }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="success" size="sm">{{ $role->users_count }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-500 dark:text-zinc-400 text-sm">{{ $role->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <flux:button wire:click="editRole({{ $role->id }})" size="sm" variant="ghost">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </span>
                                </flux:button>
                                @if(!$isProtectedRole)
                                    <x-admin.confirm-delete-button
                                        :message="__('Are you sure you want to delete this role?')"
                                        wire:click="deleteRole({{ $role->id }})"
                                        size="sm"
                                    >
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span>{{ __('Delete') }}</span>
                                        </span>
                                    </x-admin.confirm-delete-button>
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
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="5" :title="__('No roles found')" :description="__('Get started by creating your first role.')">
                        <x-slot:icon>
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </x-slot:icon>
                        <flux:button wire:click="createRole" variant="primary" size="sm">
                            {{ __('Add New Role') }}
                        </flux:button>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($roles->hasPages())
        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" name="role-modal">
            <div class="max-w-6xl mx-auto">
                <form wire:submit.prevent="saveRole" class="space-y-6">
                <flux:heading>{{ $editingId ? __('Edit Role') : __('Create Role') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Role Name') }} *</flux:label>
                    <flux:input wire:model="name" placeholder="{{ __('e.g., editor, moderator') }}" :disabled="$editingIsProtectedRole" />
                    @if($editingIsProtectedRole)
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('This role name is used by the application and is protected from changes.') }}</p>
                    @else
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Unique name for this role') }}</p>
                    @endif
                    <flux:error name="name" />
                </flux:field>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:label>{{ __('Permissions') }}</flux:label>
                        <div class="flex gap-2">
                            <flux:button
                                type="button"
                                wire:click="selectAllPermissions"
                                variant="ghost"
                                size="xs"
                            >
                                {{ __('Select All') }}
                            </flux:button>
                            <flux:button
                                type="button"
                                wire:click="clearAllPermissions"
                                variant="ghost"
                                size="xs"
                            >
                                {{ __('Clear All') }}
                            </flux:button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto pr-2 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                        @foreach($groupedPermissions as $resource => $permissions)
                            @php
                                $groupName = ucfirst($resource);
                                $groupIds = $permissions->pluck('id')->toArray();
                                $allSelected = !empty($groupIds) && count(array_intersect($selectedPermissions, $groupIds)) === count($groupIds);
                            @endphp

                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 bg-white dark:bg-zinc-900" wire:key="group-{{ $resource }}">
                                <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <h3 class="font-semibold text-sm text-zinc-900 dark:text-white">
                                        {{ $groupName }}
                                    </h3>
                                    <flux:button
                                        type="button"
                                        wire:click='toggleGroupPermissions(@json($groupIds))'
                                        variant="ghost"
                                        size="xs"
                                    >
                                        @if($allSelected)
                                            {{ __('Deselect All') }}
                                        @else
                                            {{ __('Select All') }}
                                        @endif
                                    </flux:button>
                                </div>

                                <div class="space-y-2">
                                    @foreach($permissions as $permission)
                                        <div class="flex items-center" wire:key="perm-{{ $permission->id }}">
                                            <flux:checkbox
                                                wire:model.live="selectedPermissions"
                                                value="{{ (string) $permission->id }}"
                                                label="{{ ucfirst($permission->name) }}"
                                                class="flex-1"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if($groupedPermissions->isEmpty())
                            <div class="col-span-2">
                                <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-8 text-center">
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ __('No permissions available. Create permissions first.') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <flux:error name="selectedPermissions" />
                </div>

                <div class="flex gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveRole">
                            {{ $editingId ? __('Update Role') : __('Create Role') }}
                        </span>
                        <span wire:loading wire:target="saveRole">{{ __('Saving...') }}</span>
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
            </div>
        </flux:modal>
    @endif
</div>
