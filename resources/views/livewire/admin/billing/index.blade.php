<div class="space-y-6">
    <div>
        <flux:heading>{{ __('Billing') }}</flux:heading>
        <flux:text size="sm" variant="subtle" class="mt-1">{{ __('Your current plan and usage') }}</flux:text>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                        <flux:icon.credit-card class="size-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ $tenant->plan?->name ?? __('No Plan') }}</flux:heading>
                        @if($tenant->plan)
                            <flux:text size="sm" variant="subtle">{{ $tenant->plan->priceLabel() }}</flux:text>
                        @endif
                    </div>
                </div>
                <flux:badge :variant="$tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'danger' : 'warning')">
                    {{ ucfirst($tenant->status) }}
                </flux:badge>
            </div>

            @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                <flux:callout variant="info">
                    {{ __('Your trial ends on :date (:days days left).', ['date' => $tenant->trial_ends_at->format('M d, Y'), 'days' => now()->diffInDays($tenant->trial_ends_at)]) }}
                </flux:callout>
            @endif

            @if($tenant->upgrade_requested_at)
                <flux:callout variant="warning">
                    {{ __('Upgrade to :plan requested on :date — our team will follow up shortly.', ['plan' => $tenant->desiredPlan?->name ?? __('a new plan'), 'date' => $tenant->upgrade_requested_at->format('M d, Y')]) }}
                </flux:callout>
            @else
                <form wire:submit="requestUpgrade" class="flex flex-wrap items-end gap-3">
                    <flux:field class="flex-1 min-w-[180px]">
                        <flux:label>{{ __('Upgrade to') }}</flux:label>
                        <flux:select wire:model="desiredPlanId">
                            <option value="">{{ __('Select a plan') }}</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->priceLabel() }})</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="desiredPlanId" />
                    </flux:field>
                    <flux:button type="submit" variant="primary">
                        {{ __('Request Upgrade') }}
                    </flux:button>
                </form>
            @endif
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.chart-bar class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="md" level="3">{{ __('Plan Usage') }}</flux:heading>
            </div>

            <div class="space-y-4">
                @foreach([
                    'products' => __('Products'),
                    'admin_users' => __('Admin Users'),
                    'custom_domains' => __('Custom Domains'),
                ] as $key => $label)
                    @php
                        $used = $usage[$key]['used'];
                        $limit = $usage[$key]['limit'];
                        $percent = $limit ? min(100, (int) round(($used / max($limit, 1)) * 100)) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
                            <span class="text-zinc-500 dark:text-zinc-400">{{ $used }} / {{ $limit ?? __('Unlimited') }}</span>
                        </div>
                        @if($limit)
                            <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full {{ $percent >= 100 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $percent }}%"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
