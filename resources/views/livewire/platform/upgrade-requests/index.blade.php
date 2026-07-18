<div class="space-y-6">
    <div>
        <flux:heading>{{ __('Upgrade Requests') }}</flux:heading>
        <flux:text size="sm" variant="subtle" class="mt-1">
            {{ __('Tenants waiting on a plan upgrade, oldest first') }}
        </flux:text>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tenant') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Current Plan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Desired Plan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Waiting') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($requests as $tenant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</div>
                            <div class="text-xs text-zinc-400">{{ $tenant->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="subtle" size="sm">{{ $tenant->plan?->name ?? __('None') }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge variant="info" size="sm">{{ $tenant->desiredPlan?->name ?? __('Unspecified') }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $tenant->upgrade_requested_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <flux:button :href="route('platform.tenants.show', $tenant)" size="sm" variant="ghost" wire:navigate>
                                    {{ __('View') }}
                                </flux:button>
                                <flux:button wire:click="approve({{ $tenant->id }})" size="sm" variant="primary">
                                    {{ __('Approve') }}
                                </flux:button>
                                <flux:button wire:click="reject({{ $tenant->id }})" size="sm" variant="ghost"
                                    wire:confirm="{{ __('Reject this upgrade request?') }}">
                                    {{ __('Reject') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No pending upgrade requests.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>
