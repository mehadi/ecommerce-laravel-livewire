<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class General extends Component
{
    use WithFileUploads;

    public string $site_name = '';

    public string $site_tagline = '';

    public string $site_description = '';

    public string $site_url = '';

    public $logo = null;

    public ?string $existing_logo = null;

    public $favicon = null;

    public ?string $existing_favicon = null;

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'site_name',
            'site_tagline',
            'site_description',
            'site_url',
            'site_logo',
            'site_favicon',
        ]);

        $this->site_name = $settings['site_name'] ?? config('app.name');
        $this->site_tagline = $settings['site_tagline'] ?? '';
        $this->site_description = $settings['site_description'] ?? '';
        $this->site_url = $settings['site_url'] ?? config('app.url');
        $this->existing_logo = $settings['site_logo'] ?? null;
        $this->existing_favicon = $settings['site_favicon'] ?? null;
    }

    public function removeLogo(): void
    {
        if ($this->existing_logo) {
            Storage::disk('public')->delete($this->existing_logo);
            PlatformSetting::set('site_logo', null);
            $this->existing_logo = null;
        }
    }

    public function removeFavicon(): void
    {
        if ($this->existing_favicon) {
            Storage::disk('public')->delete($this->existing_favicon);
            PlatformSetting::set('site_favicon', null);
            $this->existing_favicon = null;
        }
    }

    public function update(): void
    {
        $validated = $this->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'site_url' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:ico,png', 'max:512'],
        ], [
            'logo.image' => __('The logo must be an image file.'),
            'logo.max' => __('The logo size must not exceed 2MB.'),
            'favicon.image' => __('The favicon must be an image file.'),
            'favicon.mimes' => __('The favicon must be a .ico or .png file.'),
            'favicon.max' => __('The favicon size must not exceed 512KB.'),
        ]);

        if ($this->logo) {
            if ($this->existing_logo) {
                Storage::disk('public')->delete($this->existing_logo);
            }

            $logoPath = $this->logo->store('platform/logos', 'public');
            $validated['site_logo'] = $logoPath;
            $this->existing_logo = $logoPath;
            $this->logo = null;
        }

        if ($this->favicon) {
            if ($this->existing_favicon) {
                Storage::disk('public')->delete($this->existing_favicon);
            }

            $faviconPath = $this->favicon->store('platform/favicons', 'public');
            $validated['site_favicon'] = $faviconPath;
            $this->existing_favicon = $faviconPath;
            $this->favicon = null;
        }

        unset($validated['logo'], $validated['favicon']);

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.website-defaults.general')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
