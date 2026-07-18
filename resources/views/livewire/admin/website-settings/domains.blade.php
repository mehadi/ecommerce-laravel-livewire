<x-website-settings.layout :heading="__('Custom Domains')" :subheading="__('Connect your own domain to your storefront')">
    <div class="space-y-8">
        @unless($verificationTarget)
            <flux:callout variant="warning" icon="exclamation-triangle" heading="{{ __('Domain verification is not configured yet.') }}" text="{{ __('Contact support to enable custom domains for your store.') }}" />
        @endunless

        <!-- Add Domain Card -->
        <form wire:submit="addDomain" class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.globe-alt class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Add a Custom Domain') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Use your own domain instead of the default platform address') }}</flux:text>
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
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <flux:icon.shield-check class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Your Domains') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Verification and SSL are handled automatically once a domain is verified') }}</flux:text>
                </div>
            </div>

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
                                    wire:confirm="{{ __('Are you sure you want to remove this domain?') }}"
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
    </div>
</x-website-settings.layout>
