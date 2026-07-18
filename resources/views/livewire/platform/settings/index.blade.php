<div class="space-y-6">
    <div>
        <flux:heading>{{ __('Platform Settings') }}</flux:heading>
        <flux:text size="sm" variant="subtle" class="mt-1">
            {{ __('Global configuration for the whole platform') }}
        </flux:text>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <form wire:submit="update" class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6 max-w-2xl">
        <flux:field>
            <flux:label>{{ __('Default Trial Length (days)') }}</flux:label>
            <flux:input type="number" min="0" wire:model="default_trial_days" />
            <flux:error name="default_trial_days" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Support Contact Email') }}</flux:label>
            <flux:input type="email" wire:model="support_contact_email" placeholder="support@example.com" />
            <flux:error name="support_contact_email" />
        </flux:field>

        <flux:checkbox wire:model.live="maintenance_mode" label="{{ __('Maintenance mode') }}" />

        @if($maintenance_mode)
            <flux:field>
                <flux:label>{{ __('Maintenance Mode Message') }}</flux:label>
                <flux:textarea wire:model="maintenance_mode_message" rows="3" placeholder="{{ __('We\'ll be back shortly...') }}" />
                <flux:error name="maintenance_mode_message" />
            </flux:field>
        @endif

        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="update">{{ __('Save Settings') }}</span>
            <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
        </flux:button>
    </form>
</div>
