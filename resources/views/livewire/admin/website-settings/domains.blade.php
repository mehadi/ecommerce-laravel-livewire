<x-website-settings.layout :heading="__('Custom Domains')" :subheading="__('Connect your own domain to your storefront')">
    <div class="space-y-8">
        @unless($verificationTarget)
            <flux:callout variant="warning" icon="exclamation-triangle" heading="{{ __('Domain verification is not configured yet.') }}" text="{{ __('Contact support to enable custom domains for your store.') }}" />
        @endunless

        <!-- Add Domain Card -->
        <form wire:submit="addDomain" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-blue-600 dark:text-blue-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m-15.432 0A8.959 8.959 0 0 1 3 12c0-.778.099-1.533.284-2.253m0 0A9.004 9.004 0 0 1 12 3m0 0" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Add a Custom Domain') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Use your own domain instead of the default platform address') }}</flux:text>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>{{ __('Domain') }}</flux:label>
                    <flux:input
                        wire:model="newDomain"
                        type="text"
                        placeholder="shop.example.com"
                    />
                    <flux:description>{{ __('Enter the domain you own, without http:// or https://') }}</flux:description>
                    <flux:error name="newDomain" />
                </flux:field>
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="addDomain" class="whitespace-nowrap transition-colors">
                    <span wire:loading.remove wire:target="addDomain">{{ __('Add Domain') }}</span>
                    <span wire:loading wire:target="addDomain">{{ __('Adding...') }}</span>
                </flux:button>
            </div>
        </form>

        <!-- Domains List Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-purple-600 dark:text-purple-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Your Domains') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Verification and SSL are handled automatically once a domain is verified') }}</flux:text>
                </div>
            </div>

            @if ($domains->isEmpty())
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No custom domains added yet.') }}</flux:text>
            @else
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($domains as $domain)
                        <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $domain->domain }}</span>
                                    @if ($domain->isVerified())
                                        <flux:badge color="emerald" size="sm">{{ __('Verified') }}</flux:badge>
                                    @else
                                        <flux:badge color="amber" size="sm">{{ __('Pending') }}</flux:badge>
                                    @endif
                                </div>
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
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
                                        wire:click="recheck({{ $domain->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="recheck({{ $domain->id }})"
                                        size="sm"
                                        variant="ghost"
                                    >
                                        <span wire:loading.remove wire:target="recheck({{ $domain->id }})">{{ __('Recheck') }}</span>
                                        <span wire:loading wire:target="recheck({{ $domain->id }})">{{ __('Checking...') }}</span>
                                    </flux:button>
                                @endunless

                                <flux:button
                                    wire:click="delete({{ $domain->id }})"
                                    wire:confirm="{{ __('Are you sure you want to remove :domain? This cannot be undone.', ['domain' => $domain->domain]) }}"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $domain->id }})"
                                    size="sm"
                                    variant="danger"
                                >
                                    <span wire:loading.remove wire:target="delete({{ $domain->id }})">{{ __('Remove') }}</span>
                                    <span wire:loading wire:target="delete({{ $domain->id }})">{{ __('Removing...') }}</span>
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-website-settings.layout>
