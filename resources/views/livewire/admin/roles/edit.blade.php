<div class="max-w-6xl space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading>{{ __('Edit Role') }}</flux:heading>
        <flux:button :href="route('admin.roles.index')" wire:navigate variant="ghost" size="sm">
            {{ __('Back to Roles') }}
        </flux:button>
    </div>

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <form wire:submit="update" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Role Name') }} *</flux:label>
            <flux:input wire:model="name" placeholder="{{ __('e.g., editor, moderator') }}" />
            <flux:error name="name" />
        </flux:field>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:label>{{ __('Permissions') }}</flux:label>
                <div class="flex gap-2">
                    <button 
                        type="button"
                        wire:click="selectAllPermissions"
                        class="text-xs text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 underline"
                    >
                        {{ __('Select All') }}
                    </button>
                    <span class="text-xs text-zinc-400">|</span>
                    <button 
                        type="button"
                        wire:click="clearAllPermissions"
                        class="text-xs text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 underline"
                    >
                        {{ __('Clear All') }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[600px] overflow-y-auto pr-2">
                @foreach($groupedPermissions as $resource => $permissions)
                    @php
                        $groupName = ucfirst($resource);
                        $groupIds = $permissions->pluck('id')->toArray();
                        $allSelected = !empty($groupIds) && count(array_intersect($selectedPermissions, $groupIds)) === count($groupIds);
                        $someSelected = !empty($groupIds) && count(array_intersect($selectedPermissions, $groupIds)) > 0 && !$allSelected;
                    @endphp

                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 bg-white dark:bg-zinc-900" wire:key="group-{{ $resource }}">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="font-semibold text-sm text-zinc-900 dark:text-white">
                                {{ $groupName }}
                            </h3>
                            <button
                                type="button"
                                wire:click='toggleGroupPermissions(@json($groupIds))'
                                class="text-xs text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 underline"
                            >
                                @if($allSelected)
                                    {{ __('Deselect All') }}
                                @else
                                    {{ __('Select All') }}
                                @endif
                            </button>
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
            <flux:button type="submit" variant="primary">{{ __('Update Role') }}</flux:button>
            <flux:button :href="route('admin.roles.index')" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</div>
