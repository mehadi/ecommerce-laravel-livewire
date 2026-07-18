<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Cart & Checkout Settings') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Choose how the shopping cart and checkout appear to visitors') }}
            </flux:text>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" wire:key="success-message-{{ time() }}">{{ session('message') }}</flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <flux:icon.shopping-cart class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Shopping Cart') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Choose how the cart opens when a customer views their items') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Cart Display Mode') }}</flux:label>
                <flux:radio.group wire:model="cart_display_mode" variant="segmented">
                    <flux:radio value="modal" label="{{ __('Modal') }}" />
                    <flux:radio value="panel" label="{{ __('Right Slide Panel') }}" />
                </flux:radio.group>
                <flux:description>{{ __('Modal opens centered over the page. Right Slide Panel slides in from the right edge, like a drawer.') }}</flux:description>
                <flux:error name="cart_display_mode" />
            </flux:field>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.check-badge class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Checkout') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Choose how the checkout form opens when a customer proceeds to buy') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Checkout Display Mode') }}</flux:label>
                <flux:radio.group wire:model="checkout_display_mode" variant="segmented">
                    <flux:radio value="modal" label="{{ __('Modal') }}" />
                    <flux:radio value="panel" label="{{ __('Right Slide Panel') }}" />
                </flux:radio.group>
                <flux:description>{{ __('Modal opens centered over the page. Right Slide Panel slides in from the right edge, like a drawer.') }}</flux:description>
                <flux:error name="checkout_display_mode" />
            </flux:field>
        </div>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">{{ __('Save Settings') }}</flux:button>
        </div>
    </form>
</div>
