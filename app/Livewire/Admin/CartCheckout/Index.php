<?php

namespace App\Livewire\Admin\CartCheckout;

use App\Models\Setting;
use Livewire\Component;

class Index extends Component
{
    public string $cart_display_mode = 'modal';

    public string $checkout_display_mode = 'modal';

    public function mount(): void
    {
        $settings = Setting::getMany([
            'cart_display_mode',
            'checkout_display_mode',
        ]);

        $this->cart_display_mode = $settings['cart_display_mode'] ?? 'modal';
        $this->checkout_display_mode = $settings['checkout_display_mode'] ?? 'modal';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'cart_display_mode' => ['required', 'in:modal,panel'],
            'checkout_display_mode' => ['required', 'in:modal,panel'],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Cart & checkout settings saved successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.cart-checkout.index')
            ->layout('components.layouts.app', [
                'title' => __('Cart & Checkout Settings'),
            ]);
    }
}
