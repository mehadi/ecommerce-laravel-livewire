<?php

namespace App\Livewire\Platform;

use App\Models\Plan;
use Livewire\Component;

class Landing extends Component
{
    public function render()
    {
        return view('livewire.platform.landing', [
            'plans' => Plan::orderBy('sort_order')->get(),
        ])->layout('components.layouts.platform', [
            'title' => __('Launch Your Online Store in Minutes'),
            'metaDescription' => __('Build, launch, and grow your online store — landing pages, product catalog, custom domains, and secure checkout, all in one platform.'),
        ]);
    }
}
