<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Seo extends Component
{
    use WithFileUploads;

    // SEO Settings
    public string $meta_description = '';

    public string $meta_keywords = '';

    public $og_image = null;

    public ?string $existing_og_image = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $settings = Setting::getMany([
            'meta_description',
            'meta_keywords',
            'site_og_image',
        ]);

        $this->meta_description = $settings['meta_description'] ?? '';
        $this->meta_keywords = $settings['meta_keywords'] ?? '';
        $this->existing_og_image = $settings['site_og_image'] ?? null;
    }

    /**
     * Remove Open Graph image.
     */
    public function removeOgImage(): void
    {
        if ($this->existing_og_image) {
            Storage::disk('public')->delete($this->existing_og_image);
            Setting::set('site_og_image', null);
            $this->existing_og_image = null;
        }
    }

    /**
     * Update SEO settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'max:2048'],
        ], [
            'og_image.image' => __('The Open Graph image must be an image file.'),
            'og_image.max' => __('The Open Graph image size must not exceed 2MB.'),
        ]);

        // Handle Open Graph image upload
        if ($this->og_image) {
            // Delete old OG image if exists
            if ($this->existing_og_image) {
                Storage::disk('public')->delete($this->existing_og_image);
            }

            // Store new OG image
            $ogImagePath = $this->og_image->store('og-images', 'public');
            $validated['site_og_image'] = $ogImagePath;
            $this->existing_og_image = $ogImagePath;
            $this->og_image = null;
        }

        // Remove file upload keys from validated if they're not set
        unset($validated['og_image']);

        Setting::setMany($validated);

        // Clear relevant caches
        Cache::forget('landing.sections.hero');
        Cache::forget('landing.sections.features');
        Cache::forget('landing.sections.faq');

        session()->flash('message', __('Website settings updated successfully.'));
    }

    /**
     * Get computed meta description length.
     */
    public function getMetaDescriptionLengthProperty(): int
    {
        return mb_strlen($this->meta_description);
    }

    /**
     * Get SEO recommendation color for meta description.
     */
    public function getMetaDescriptionColorProperty(): string
    {
        $length = $this->metaDescriptionLength;

        if ($length === 0) {
            return 'text-gray-500';
        }

        if ($length < 120) {
            return 'text-yellow-600 dark:text-yellow-400';
        }

        if ($length <= 160) {
            return 'text-green-600 dark:text-green-400';
        }

        return 'text-red-600 dark:text-red-400';
    }

    public function render()
    {
        return view('livewire.admin.website-settings.seo')
            ->layout('components.layouts.app', [
                'title' => __('SEO Settings'),
            ]);
    }
}
