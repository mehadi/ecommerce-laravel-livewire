<div class="space-y-6">
    <x-admin.page-header :heading="__('Cycle Counts')" :description="__('Reconcile system stock against a physical count — scope to everything for a full physical inventory, or narrow by category/ABC class for a rotating cycle count')">
        <flux:button wire:click="openCreateModal" variant="primary">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>{{ __('New Cycle Count') }}</span>
            </span>
        </flux:button>
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <div class="flex flex-wrap gap-4 items-end">
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Status') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="in_progress">{{ __('In Progress') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Scope') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Items') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Created') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($cycleCounts as $cycleCount)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $cycleCount->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $cycleCount->scope === 'all' ? __('All Products (Physical Inventory)') : ucfirst(str_replace('_', ' ', $cycleCount->scope)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ __(':count item(s)', ['count' => $cycleCount->items->count()]) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge size="sm" :variant="match($cycleCount->status) { 'completed' => 'success', 'in_progress' => 'info', default => 'warning' }">
                                {{ ucfirst(str_replace('_', ' ', $cycleCount->status)) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $cycleCount->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cycleCount->canBeCounted())
                                <flux:button :href="route('admin.cycle-counts.count', $cycleCount)" size="sm" variant="primary" wire:navigate>
                                    {{ __('Count') }}
                                </flux:button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state colspan="6" :title="__('No cycle counts yet')" :description="__('Create one to reconcile system stock against a physical count.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cycleCounts->hasPages())
        <div class="mt-4">
            {{ $cycleCounts->links() }}
        </div>
    @endif

    @if($showCreateModal)
        <flux:modal wire:model="showCreateModal" name="cycle-count-modal">
            <form wire:submit.prevent="createCycleCount" class="space-y-6">
                <flux:heading>{{ __('New Cycle Count') }}</flux:heading>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Warehouse') }}</flux:label>
                    <flux:select wire:model="warehouse_id">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="warehouse_id" />
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Scope') }}</flux:label>
                    <flux:select wire:model.live="scope">
                        <option value="all">{{ __('All products (physical inventory)') }}</option>
                        <option value="category">{{ __('By category') }}</option>
                        <option value="abc_class">{{ __('By ABC class') }}</option>
                    </flux:select>
                    <flux:error name="scope" />
                </flux:field>

                @if($scope === 'category')
                    <flux:field>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <flux:select wire:model="filterCategoryId">
                            <option value="">{{ __('Select category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name_en }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="filterCategoryId" />
                    </flux:field>
                @endif

                @if($scope === 'abc_class')
                    <flux:field>
                        <flux:label>{{ __('ABC Class') }}</flux:label>
                        <flux:select wire:model="filterAbcClass">
                            <option value="">{{ __('Select class') }}</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </flux:select>
                        <flux:error name="filterAbcClass" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ __('Notes') }}</flux:label>
                    <flux:textarea wire:model="notes" rows="2" />
                    <flux:error name="notes" />
                </flux:field>

                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createCycleCount">
                        {{ __('Create Cycle Count') }}
                    </flux:button>
                    <flux:button type="button" wire:click="closeCreateModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
