<div class="space-y-6">
    <x-admin.page-header :heading="__('Cart & Checkout Settings')" :description="__('Choose how the shopping cart and checkout appear to visitors')" />

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <flux:icon.shopping-cart class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Shopping Cart') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Choose how the cart opens when a customer views their items') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Cart Display Mode') }}</flux:label>
                <flux:radio.group wire:model="cart_display_mode" variant="cards">
                    <flux:radio value="modal" class="items-start">
                        <div class="flex-1 space-y-3">
                            <x-admin.display-mode-preview mode="modal" />
                            <div>
                                <flux:heading size="sm">{{ __('Modal') }}</flux:heading>
                                <flux:text size="sm" variant="subtle">{{ __('Opens centered over the page') }}</flux:text>
                            </div>
                        </div>
                        <flux:radio.indicator />
                    </flux:radio>
                    <flux:radio value="panel" class="items-start">
                        <div class="flex-1 space-y-3">
                            <x-admin.display-mode-preview mode="panel" />
                            <div>
                                <flux:heading size="sm">{{ __('Right Slide Panel') }}</flux:heading>
                                <flux:text size="sm" variant="subtle">{{ __('Slides in from the right edge, like a drawer') }}</flux:text>
                            </div>
                        </div>
                        <flux:radio.indicator />
                    </flux:radio>
                </flux:radio.group>
                <flux:error name="cart_display_mode" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Cart Style') }}</flux:label>
                @include('livewire.admin.website-settings._variant-picker', [
                    'variants' => $cartVariants,
                    'wireModel' => 'storefront_cart_variant',
                    'selected' => $storefront_cart_variant,
                    'previewView' => 'livewire.admin.cart-checkout.cart-preview',
                    'defaultKey' => \App\Support\CartVariants::DEFAULT,
                ])
                <flux:error name="storefront_cart_variant" />
            </flux:field>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.check-badge class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Checkout') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Choose how the checkout form opens when a customer proceeds to buy') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Checkout Display Mode') }}</flux:label>
                <flux:radio.group wire:model="checkout_display_mode" variant="cards">
                    <flux:radio value="modal" class="items-start">
                        <div class="flex-1 space-y-3">
                            <x-admin.display-mode-preview mode="modal" />
                            <div>
                                <flux:heading size="sm">{{ __('Modal') }}</flux:heading>
                                <flux:text size="sm" variant="subtle">{{ __('Opens centered over the page') }}</flux:text>
                            </div>
                        </div>
                        <flux:radio.indicator />
                    </flux:radio>
                    <flux:radio value="panel" class="items-start">
                        <div class="flex-1 space-y-3">
                            <x-admin.display-mode-preview mode="panel" />
                            <div>
                                <flux:heading size="sm">{{ __('Right Slide Panel') }}</flux:heading>
                                <flux:text size="sm" variant="subtle">{{ __('Slides in from the right edge, like a drawer') }}</flux:text>
                            </div>
                        </div>
                        <flux:radio.indicator />
                    </flux:radio>
                </flux:radio.group>
                <flux:error name="checkout_display_mode" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Checkout Style') }}</flux:label>
                @include('livewire.admin.website-settings._variant-picker', [
                    'variants' => $checkoutVariants,
                    'wireModel' => 'storefront_checkout_variant',
                    'selected' => $storefront_checkout_variant,
                    'previewView' => 'livewire.admin.cart-checkout.checkout-preview',
                    'defaultKey' => \App\Support\CheckoutVariants::DEFAULT,
                ])
                <flux:error name="storefront_checkout_variant" />
            </flux:field>
        </div>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ __('Save Settings') }}</span>
                <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</div>
