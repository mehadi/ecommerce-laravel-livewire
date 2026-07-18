<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\PhoneFormats;
use Livewire\Component;

class Localization extends Component
{
    public string $currency_code = 'BDT';

    public string $currency_symbol = '৳';

    public string $phone_format_preset = PhoneFormats::DEFAULT_PRESET;

    public function mount(): void
    {
        $settings = Setting::getMany([
            'currency_code',
            'currency_symbol',
            'phone_format_preset',
        ]);

        $this->currency_code = $settings['currency_code'] ?? 'BDT';
        $this->currency_symbol = $settings['currency_symbol'] ?? '৳';
        $this->phone_format_preset = $settings['phone_format_preset'] ?? PhoneFormats::DEFAULT_PRESET;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'currency_code' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'phone_format_preset' => ['required', 'string', 'in:'.implode(',', array_keys(PhoneFormats::PRESETS))],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Localization settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.localization', [
            'phonePresets' => PhoneFormats::PRESETS,
        ])->layout('components.layouts.app', [
            'title' => __('Localization'),
        ]);
    }
}
