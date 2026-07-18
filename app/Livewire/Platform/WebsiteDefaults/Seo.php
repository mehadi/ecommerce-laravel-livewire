<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Seo extends Component
{
    use WithFileUploads;

    public string $meta_description = '';

    public string $meta_keywords = '';

    public $og_image = null;

    public ?string $existing_og_image = null;

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'meta_description',
            'meta_keywords',
            'site_og_image',
        ]);

        $this->meta_description = $settings['meta_description'] ?? '';
        $this->meta_keywords = $settings['meta_keywords'] ?? '';
        $this->existing_og_image = $settings['site_og_image'] ?? null;
    }

    public function removeOgImage(): void
    {
        if ($this->existing_og_image) {
            Storage::disk('public')->delete($this->existing_og_image);
            PlatformSetting::set('site_og_image', null);
            $this->existing_og_image = null;
        }
    }

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

        if ($this->og_image) {
            if ($this->existing_og_image) {
                Storage::disk('public')->delete($this->existing_og_image);
            }

            $ogImagePath = $this->og_image->store('platform/og-images', 'public');
            $validated['site_og_image'] = $ogImagePath;
            $this->existing_og_image = $ogImagePath;
            $this->og_image = null;
        }

        unset($validated['og_image']);

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function getMetaDescriptionLengthProperty(): int
    {
        return mb_strlen($this->meta_description);
    }

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
        return view('livewire.platform.website-defaults.seo')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
