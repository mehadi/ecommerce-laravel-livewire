<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ $tenant->name }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">{{ $tenant->slug }}</flux:text>
        </div>
        <flux:button :href="route('platform.tenants.index')" variant="ghost" wire:navigate>
            {{ __('Back to Tenants') }}
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if ($tenant->upgrade_requested_at)
        <flux:callout variant="warning">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <span>
                    {{ __('Upgrade requested to :plan on :date.', ['plan' => $tenant->desiredPlan?->name ?? __('an unspecified plan'), 'date' => $tenant->upgrade_requested_at->format('M d, Y')]) }}
                </span>
                <div class="flex gap-2">
                    <flux:button wire:click="approveUpgrade" size="sm" variant="primary">{{ __('Approve') }}</flux:button>
                    <flux:button wire:click="rejectUpgrade" size="sm" variant="ghost" wire:confirm="{{ __('Reject this upgrade request?') }}">{{ __('Reject') }}</flux:button>
                </div>
            </div>
        </flux:callout>
    @endif

    <div class="flex flex-wrap gap-3">
        @can('impersonate tenants')
            @if($tenant->owner)
                <flux:button wire:click="impersonate" variant="ghost">
                    {{ __('Log in as tenant owner') }}
                </flux:button>
            @endif
        @endcan
    </div>

    {{-- Tenant Details --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
        <flux:heading size="md" level="3">{{ __('Tenant Details') }}</flux:heading>

        <form wire:submit="updateDetails" class="grid gap-5 md:grid-cols-2 items-start">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Slug') }}</flux:label>
                <flux:input wire:model="slug" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Used as the subdomain: slug.yourdomain.com') }}</p>
                <flux:error name="slug" />
            </flux:field>

            <div class="md:col-span-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="updateDetails">{{ __('Save Details') }}</span>
                    <span wire:loading wire:target="updateDetails">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    </div>

    {{-- Domain & DNS --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
        <flux:heading size="md" level="3">{{ __('Domain & DNS') }}</flux:heading>
        <flux:text size="sm" variant="subtle">{{ __('Connect a custom domain to this tenant\'s storefront. Verification and SSL are handled automatically once the DNS record is in place.') }}</flux:text>

        @unless($verificationTarget)
            <flux:callout variant="warning" icon="exclamation-triangle" heading="{{ __('Domain verification target is not configured.') }}" text="{{ __('Set TENANT_DOMAIN_TARGET to enable custom domains.') }}" />
        @endunless

        <form wire:submit="addDomain" class="flex flex-wrap items-end gap-4">
            <flux:field class="grow min-w-[240px]">
                <flux:label>{{ __('Domain') }}</flux:label>
                <flux:input wire:model="newDomain" type="text" placeholder="shop.example.com" />
                <flux:description>{{ __('Enter the domain the tenant owns, without http:// or https://') }}</flux:description>
                <flux:error name="newDomain" />
            </flux:field>

            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="addDomain">
                <span wire:loading.remove wire:target="addDomain">{{ __('Add Domain') }}</span>
                <span wire:loading wire:target="addDomain">{{ __('Adding...') }}</span>
            </flux:button>
        </form>

        @if ($domains->isEmpty())
            <flux:text class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('No custom domains added yet.') }}</flux:text>
        @else
            <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach ($domains as $domain)
                    <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-neutral-900 dark:text-white">{{ $domain->domain }}</span>
                                @if ($domain->isVerified())
                                    <flux:badge color="green" size="sm">{{ __('Verified') }}</flux:badge>
                                @else
                                    <flux:badge color="amber" size="sm">{{ __('Pending') }}</flux:badge>
                                @endif
                            </div>
                            <flux:text class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                                @if ($domain->isVerified())
                                    {{ __('Verified on :date', ['date' => $domain->verified_at->format('M j, Y g:i A')]) }}
                                @else
                                    {{ __('Add a CNAME record pointing to :target to verify this domain.', ['target' => $verificationTarget ?? '—']) }}
                                @endif
                            </flux:text>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @unless ($domain->isVerified())
                                <flux:button
                                    wire:click="recheckDomain({{ $domain->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="recheckDomain({{ $domain->id }})"
                                    size="sm"
                                    variant="ghost"
                                >
                                    <span wire:loading.remove wire:target="recheckDomain({{ $domain->id }})">{{ __('Recheck') }}</span>
                                    <span wire:loading wire:target="recheckDomain({{ $domain->id }})">{{ __('Checking...') }}</span>
                                </flux:button>
                            @endunless

                            <flux:button
                                wire:click="deleteDomain({{ $domain->id }})"
                                wire:confirm="{{ __('Remove this domain from the tenant?') }}"
                                size="sm"
                                variant="danger"
                            >
                                {{ __('Remove') }}
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Subscription --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <flux:heading size="md" level="3">{{ __('Subscription') }}</flux:heading>

            <form wire:submit="updateSubscription" class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Plan') }}</flux:label>
                    <flux:select wire:model="plan_id">
                        <option value="">{{ __('No plan') }}</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->priceLabel() }})</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="plan_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <flux:select wire:model="status">
                        <option value="active">{{ __('Active') }}</option>
                        <option value="suspended">{{ __('Suspended') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Trial Ends') }}</flux:label>
                    <flux:input wire:model="trial_ends_at" type="date" />
                    <flux:error name="trial_ends_at" />
                </flux:field>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="updateSubscription">{{ __('Save Subscription') }}</span>
                    <span wire:loading wire:target="updateSubscription">{{ __('Saving...') }}</span>
                </flux:button>
            </form>
        </div>

        {{-- Usage --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <flux:heading size="md" level="3">{{ __('Plan Usage') }}</flux:heading>

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
                                <div class="h-full bg-emerald-500" style="width: {{ $percent }}%"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Danger zone --}}
    <div class="rounded-xl border border-red-200 dark:border-red-900/40 bg-white dark:bg-zinc-900 p-6 space-y-4">
        <flux:heading size="md" level="3">{{ __('Danger Zone') }}</flux:heading>

        @if($tenant->status !== 'suspended')
            <flux:text size="sm" variant="subtle">{{ __('Suspending blocks this tenant\'s storefront and admin access immediately.') }}</flux:text>
            <flux:field>
                <flux:label>{{ __('Reason') }}</flux:label>
                <flux:textarea wire:model="suspendReason" rows="2" placeholder="{{ __('Why is this tenant being suspended?') }}" />
                <flux:error name="suspendReason" />
            </flux:field>
            <flux:button wire:click="suspend" variant="danger" wire:confirm="{{ __('Suspend this tenant? Their storefront and admin will become inaccessible.') }}">
                {{ __('Suspend Tenant') }}
            </flux:button>
        @else
            <flux:text size="sm" variant="subtle">{{ __('This tenant is currently suspended.') }}</flux:text>
            <flux:button wire:click="reactivate" variant="primary" wire:confirm="{{ __('Reactivate this tenant?') }}">
                {{ __('Reactivate Tenant') }}
            </flux:button>
        @endif

        @if($tenant->status === 'cancelled')
            <div class="pt-4 border-t border-red-200 dark:border-red-900/40 space-y-2">
                <flux:text size="sm" variant="subtle">
                    {{ __('Permanently delete this tenant, its owner account, and all of its data (products, orders, domains). This cannot be undone.') }}
                </flux:text>
                <flux:button wire:click="deleteTenant" variant="danger"
                    wire:confirm="{{ __('Permanently delete this tenant and ALL of its data (products, orders, users, domains)? This cannot be undone.') }}">
                    {{ __('Delete Tenant Permanently') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Manual billing --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
        <flux:heading size="md" level="3">{{ __('Record a Manual Payment') }}</flux:heading>
        <flux:text size="sm" variant="subtle">{{ __('No payment gateway is connected — record payments received outside the platform (bank transfer, cash, etc.) here for your own records.') }}</flux:text>

        <form wire:submit="recordPayment" class="grid gap-5 md:grid-cols-2 items-start">
            <flux:field>
                <flux:label>{{ __('Amount') }}</flux:label>
                <flux:input wire:model="payment_amount" type="number" step="0.01" min="0" placeholder="29.00" />
                <flux:error name="payment_amount" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Note') }}</flux:label>
                <flux:input wire:model="payment_note" type="text" placeholder="{{ __('e.g. bank transfer ref #1234') }}" />
                <flux:error name="payment_note" />
            </flux:field>

            <div class="md:col-span-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="recordPayment">{{ __('Record Payment') }}</span>
                    <span wire:loading wire:target="recordPayment">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading size="md" level="3">{{ __('Billing History') }}</flux:heading>
        </div>
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($billingEvents as $event)
                <div class="px-6 py-3 flex items-center justify-between gap-4">
                    <div>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($event->type)) }}</span>
                        @if($event->amount !== null)
                            <span class="text-emerald-600 dark:text-emerald-400">{{ number_format($event->amount, 2) }}</span>
                        @endif
                        @if($event->note)
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $event->note }}</p>
                        @endif
                    </div>
                    <div class="text-right text-xs text-zinc-400">
                        <div>{{ $event->created_at->format('M d, Y H:i') }}</div>
                        @if($event->recordedBy)
                            <div>{{ $event->recordedBy->name }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('No billing history yet.') }}
                </div>
            @endforelse
        </div>
        @if($billingEvents->hasPages())
            <div class="px-6 py-4">
                {{ $billingEvents->links() }}
            </div>
        @endif
    </div>
</div>
