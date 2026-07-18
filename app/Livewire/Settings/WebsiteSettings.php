<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class WebsiteSettings extends Component
{
    use WithFileUploads;

    // Site Information
    public string $site_name = '';

    public string $site_tagline = '';

    public string $site_description = '';

    public $logo = null;

    public ?string $existing_logo = null;

    // Contact Information
    public string $contact_email = '';

    public string $contact_phone = '';

    public string $contact_address = '';

    // Social Media Links
    public string $social_facebook = '';

    public string $social_instagram = '';

    public string $social_twitter = '';

    public string $social_linkedin = '';

    public string $social_youtube = '';

    // Facebook Pixel
    public string $facebook_pixel_id = '';

    // Google Analytics & Tag Manager
    public string $google_analytics_id = '';

    public string $google_tag_manager_id = '';

    // Site Verification
    public string $google_verification_code = '';

    public string $bing_verification_code = '';

    // SEO Settings
    public string $meta_description = '';

    public string $meta_keywords = '';

    public string $site_url = '';

    // Favicon & Open Graph
    public $favicon = null;

    public ?string $existing_favicon = null;

    public $og_image = null;

    public ?string $existing_og_image = null;

    // Additional Social Media
    public string $social_tiktok = '';

    public string $social_pinterest = '';

    public string $social_whatsapp = '';

    // Appearance
    public string $frontend_text_size = 'medium';

    public int $frontend_text_size_custom = 100;

    /** Named text-size presets mapped to a root font-size percentage. */
    public const TEXT_SIZE_PRESETS = [
        'xs' => 80,
        'sm' => 90,
        'medium' => 100,
        'lg' => 112.5,
        'xl' => 125,
        'xxl' => 137.5,
    ];

    public string $frontend_content_width = 'medium';

    public int $frontend_content_width_custom = 1152;

    /** Named content-width presets mapped to a max-width in pixels. */
    public const CONTENT_WIDTH_PRESETS = [
        'narrow' => 960,
        'medium' => 1152,
        'wide' => 1280,
        'xl' => 1440,
        'xxl' => 1600,
        'full' => 1920,
    ];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $settings = Setting::getMany([
            'site_name',
            'site_tagline',
            'site_description',
            'site_logo',
            'site_url',
            'contact_email',
            'contact_phone',
            'contact_address',
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_linkedin',
            'social_youtube',
            'social_tiktok',
            'social_pinterest',
            'social_whatsapp',
            'facebook_pixel_id',
            'google_analytics_id',
            'google_tag_manager_id',
            'google_verification_code',
            'bing_verification_code',
            'meta_description',
            'meta_keywords',
            'site_favicon',
            'site_og_image',
            'frontend_text_size',
            'frontend_text_size_custom',
            'frontend_content_width',
            'frontend_content_width_custom',
        ]);

        $this->site_name = $settings['site_name'] ?? config('app.name');
        $this->site_tagline = $settings['site_tagline'] ?? '';
        $this->site_description = $settings['site_description'] ?? '';
        $this->site_url = $settings['site_url'] ?? config('app.url');
        $this->existing_logo = $settings['site_logo'] ?? null;
        $this->existing_favicon = $settings['site_favicon'] ?? null;
        $this->existing_og_image = $settings['site_og_image'] ?? null;
        $this->contact_email = $settings['contact_email'] ?? '';
        $this->contact_phone = $settings['contact_phone'] ?? '';
        $this->contact_address = $settings['contact_address'] ?? '';
        $this->social_facebook = $settings['social_facebook'] ?? '';
        $this->social_instagram = $settings['social_instagram'] ?? '';
        $this->social_twitter = $settings['social_twitter'] ?? '';
        $this->social_linkedin = $settings['social_linkedin'] ?? '';
        $this->social_youtube = $settings['social_youtube'] ?? '';
        $this->social_tiktok = $settings['social_tiktok'] ?? '';
        $this->social_pinterest = $settings['social_pinterest'] ?? '';
        $this->social_whatsapp = $settings['social_whatsapp'] ?? '';
        $this->facebook_pixel_id = $settings['facebook_pixel_id'] ?? '';
        $this->google_analytics_id = $settings['google_analytics_id'] ?? '';
        $this->google_tag_manager_id = $settings['google_tag_manager_id'] ?? '';
        $this->google_verification_code = $settings['google_verification_code'] ?? '';
        $this->bing_verification_code = $settings['bing_verification_code'] ?? '';
        $this->meta_description = $settings['meta_description'] ?? '';
        $this->meta_keywords = $settings['meta_keywords'] ?? '';
        $this->frontend_text_size = $settings['frontend_text_size'] ?? 'medium';
        $this->frontend_text_size_custom = isset($settings['frontend_text_size_custom'])
            ? (int) $settings['frontend_text_size_custom']
            : 100;
        $this->frontend_content_width = $settings['frontend_content_width'] ?? 'medium';
        $this->frontend_content_width_custom = isset($settings['frontend_content_width_custom'])
            ? (int) $settings['frontend_content_width_custom']
            : 1152;
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
            'og_image' => ['nullable', 'image', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_pinterest' => ['nullable', 'url', 'max:255'],
            'social_whatsapp' => ['nullable', 'string', 'max:255'],
            'facebook_pixel_id' => ['nullable', 'string', 'max:255', 'regex:/^\d+$/'],
            'google_analytics_id' => ['nullable', 'string', 'max:255', 'regex:/^G-[A-Z0-9]+$|^UA-\d+-\d+$/'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:255', 'regex:/^GTM-[A-Z0-9]+$/'],
            'google_verification_code' => ['nullable', 'string', 'max:255'],
            'bing_verification_code' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'frontend_text_size' => ['required', 'in:xs,sm,medium,lg,xl,xxl,custom'],
            'frontend_text_size_custom' => ['required_if:frontend_text_size,custom', 'integer', 'min:50', 'max:200'],
            'frontend_content_width' => ['required', 'in:narrow,medium,wide,xl,xxl,full,custom'],
            'frontend_content_width_custom' => ['required_if:frontend_content_width,custom', 'integer', 'min:960', 'max:1920'],
        ], [
            'facebook_pixel_id.regex' => __('The Facebook Pixel ID must contain only numbers.'),
            'google_analytics_id.regex' => __('The Google Analytics ID format is invalid. Use format: G-XXXXXXXXXX or UA-XXXXXX-X'),
            'google_tag_manager_id.regex' => __('The Google Tag Manager ID format is invalid. Use format: GTM-XXXXXXX'),
            'logo.image' => __('The logo must be an image file.'),
            'logo.max' => __('The logo size must not exceed 2MB.'),
            'favicon.image' => __('The favicon must be an image file.'),
            'favicon.mimes' => __('The favicon must be a .ico or .png file.'),
            'favicon.max' => __('The favicon size must not exceed 512KB.'),
            'og_image.image' => __('The Open Graph image must be an image file.'),
            'og_image.max' => __('The Open Graph image size must not exceed 2MB.'),
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
        unset($validated['logo'], $validated['favicon'], $validated['og_image']);

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
        return view('livewire.settings.website-settings')
            ->layout('components.layouts.app', [
                'title' => __('Website Settings'),
            ]);
    }
}
