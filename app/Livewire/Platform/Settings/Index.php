<?php

namespace App\Livewire\Platform\Settings;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    public string $default_trial_days = '14';

    public string $support_contact_email = '';

    public bool $maintenance_mode = false;

    public string $maintenance_mode_message = '';

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'default_trial_days',
            'support_contact_email',
            'maintenance_mode',
            'maintenance_mode_message',
        ]);

        $this->default_trial_days = $settings['default_trial_days'] ?? '14';
        $this->support_contact_email = $settings['support_contact_email'] ?? '';
        $this->maintenance_mode = (bool) ($settings['maintenance_mode'] ?? false);
        $this->maintenance_mode_message = $settings['maintenance_mode_message'] ?? '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'default_trial_days' => ['required', 'integer', 'min:0'],
            'support_contact_email' => ['nullable', 'email', 'max:255'],
            'maintenance_mode' => ['boolean'],
            'maintenance_mode_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['maintenance_mode'] = $validated['maintenance_mode'] ? '1' : '0';

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.settings.index')
            ->layout('components.layouts.app', [
                'title' => __('Platform Settings'),
            ]);
    }
}
