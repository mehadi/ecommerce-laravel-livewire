<?php

namespace App\Livewire\Admin\CartCheckout;

use App\Models\Setting;
use App\Support\CartVariants;
use App\Support\CheckoutVariants;
use Livewire\Component;

class Index extends Component
{
    public string $cart_display_mode = 'modal';

    public string $checkout_display_mode = 'modal';

    public string $storefront_cart_variant = CartVariants::DEFAULT;

    public string $storefront_checkout_variant = CheckoutVariants::DEFAULT;

    public function mount(): void
    {
        $settings = Setting::getMany([
            'cart_display_mode',
            'checkout_display_mode',
            'storefront_cart_variant',
            'storefront_checkout_variant',
        ]);

        $this->cart_display_mode = $settings['cart_display_mode'] ?? 'modal';
        $this->checkout_display_mode = $settings['checkout_display_mode'] ?? 'modal';
        $this->storefront_cart_variant = CartVariants::resolve($settings['storefront_cart_variant']);
        $this->storefront_checkout_variant = CheckoutVariants::resolve($settings['storefront_checkout_variant']);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'cart_display_mode' => ['required', 'in:modal,panel'],
            'checkout_display_mode' => ['required', 'in:modal,panel'],
            'storefront_cart_variant' => ['required', 'in:'.implode(',', CartVariants::keys())],
            'storefront_checkout_variant' => ['required', 'in:'.implode(',', CheckoutVariants::keys())],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Cart & checkout settings saved successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.cart-checkout.index', [
            'cartVariants' => CartVariants::all(),
            'checkoutVariants' => CheckoutVariants::all(),
        ])
            ->layout('components.layouts.app', [
                'title' => __('Cart & Checkout Settings'),
            ]);
    }
}
