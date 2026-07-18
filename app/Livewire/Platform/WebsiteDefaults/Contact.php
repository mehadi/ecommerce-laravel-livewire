<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Contact extends Component
{
    public string $contact_email = '';

    public string $contact_phone = '';

    public string $contact_address = '';

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'contact_email',
            'contact_phone',
            'contact_address',
        ]);

        $this->contact_email = $settings['contact_email'] ?? '';
        $this->contact_phone = $settings['contact_phone'] ?? '';
        $this->contact_address = $settings['contact_address'] ?? '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string'],
        ]);

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.website-defaults.contact')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
