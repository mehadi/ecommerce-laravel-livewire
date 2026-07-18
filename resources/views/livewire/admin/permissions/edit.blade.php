<div class="max-w-4xl space-y-6">
    <flux:heading>{{ __('Edit Permission') }}</flux:heading>

    <form wire:submit="update" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Permission Name') }} *</flux:label>
            <flux:input wire:model="name" placeholder="{{ __('e.g., create products, edit users') }}" />
            <flux:error name="name" />
            <flux:description>{{ __('Use lowercase with spaces or dots. Example: create.products, edit.users') }}</flux:description>
        </flux:field>

        <div class="flex gap-4">
            <flux:button type="submit" variant="primary">{{ __('Update Permission') }}</flux:button>
            <flux:button :href="route('admin.permissions.index')" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</div>
