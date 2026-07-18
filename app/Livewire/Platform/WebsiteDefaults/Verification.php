<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Verification extends Component
{
    public string $google_verification_code = '';

    public string $bing_verification_code = '';

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'google_verification_code',
            'bing_verification_code',
        ]);

        $this->google_verification_code = $settings['google_verification_code'] ?? '';
        $this->bing_verification_code = $settings['bing_verification_code'] ?? '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'google_verification_code' => ['nullable', 'string', 'max:255'],
            'bing_verification_code' => ['nullable', 'string', 'max:255'],
        ]);

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.website-defaults.verification')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
