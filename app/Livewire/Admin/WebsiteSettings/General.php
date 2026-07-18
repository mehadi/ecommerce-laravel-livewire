<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class General extends Component
{
    use WithFileUploads;

    // Site Information
    public string $site_name = '';

    public string $site_tagline = '';

    public string $site_description = '';

    public string $site_url = '';

    public $logo = null;

    public ?string $existing_logo = null;

    public $favicon = null;

    public ?string $existing_favicon = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $settings = Setting::getMany([
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

    /**
     * Remove logo.
     */
    public function removeLogo(): void
    {
        if ($this->existing_logo) {
            Storage::disk('public')->delete($this->existing_logo);
            Setting::set('site_logo', null);
            $this->existing_logo = null;
        }
    }

    /**
     * Remove favicon.
     */
    public function removeFavicon(): void
    {
        if ($this->existing_favicon) {
            Storage::disk('public')->delete($this->existing_favicon);
            Setting::set('site_favicon', null);
            $this->existing_favicon = null;
        }
    }

    /**
     * Update website settings.
     */
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

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo if exists
            if ($this->existing_logo) {
                Storage::disk('public')->delete($this->existing_logo);
            }

            // Store new logo
            $logoPath = $this->logo->store('logos', 'public');
            $validated['site_logo'] = $logoPath;
            $this->existing_logo = $logoPath;
            $this->logo = null;
        }

        // Handle favicon upload
        if ($this->favicon) {
            // Delete old favicon if exists
            if ($this->existing_favicon) {
                Storage::disk('public')->delete($this->existing_favicon);
            }

            // Store new favicon
            $faviconPath = $this->favicon->store('favicons', 'public');
            $validated['site_favicon'] = $faviconPath;
            $this->existing_favicon = $faviconPath;
            $this->favicon = null;
        }

        // Remove file upload keys from validated if they're not set
        unset($validated['logo'], $validated['favicon']);

        Setting::setMany($validated);

        // Clear relevant caches
        Cache::forget('landing.sections.hero');
        Cache::forget('landing.sections.features');
        Cache::forget('landing.sections.faq');

        session()->flash('message', __('Website settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.general')
            ->layout('components.layouts.app', [
                'title' => __('Site Information'),
            ]);
    }
}
