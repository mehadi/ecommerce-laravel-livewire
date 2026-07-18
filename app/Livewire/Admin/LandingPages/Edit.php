<?php

namespace App\Livewire\Admin\LandingPages;

use App\Models\LandingPageConfig;
use App\Models\LandingPageSection;
use App\Models\Product;
use App\Models\Testimonial;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Edit extends Component
{
    public LandingPageConfig $landingPage;

    public $name = '';

    public $slug = '';

    public $slugManuallyEdited = false;

    public $product_id = null;

    public $meta_title = '';

    public $meta_description = '';

    public array $config = [
        'hero_title' => '',
        'hero_content' => '',
        'hero_badge_text' => '',
        'features_section_ids' => [],
        'testimonial_ids' => [],
        'faq_section_ids' => [],
        'show_trust_badges' => true,
        'show_product_details' => true,
        'show_features' => true,
        'show_testimonials' => true,
        'show_faq' => true,
        'show_cta' => true,
    ];

    public $is_active = true;

    public $order = 0;

    public $featureSectionSearch = '';

    public $testimonialSearch = '';

    public $faqSectionSearch = '';

    public function mount(LandingPageConfig $landingPage): void
    {
        $this->landingPage = $landingPage;
        $this->name = $landingPage->name;
        $this->slug = $landingPage->slug;
        $this->product_id = $landingPage->product_id;
        $this->meta_title = $landingPage->meta_title ?? '';
        $this->meta_description = $landingPage->meta_description ?? '';

        $savedConfig = $landingPage->config ?? [];
        // Remove hero_section_id if it exists (no longer used)
        unset($savedConfig['hero_section_id']);
        // Convert integer IDs to strings for Livewire checkboxes
        if (isset($savedConfig['features_section_ids']) && is_array($savedConfig['features_section_ids'])) {
            $savedConfig['features_section_ids'] = array_map('strval', $savedConfig['features_section_ids']);
        }
        if (isset($savedConfig['testimonial_ids']) && is_array($savedConfig['testimonial_ids'])) {
            $savedConfig['testimonial_ids'] = array_map('strval', $savedConfig['testimonial_ids']);
        }
        if (isset($savedConfig['faq_section_ids']) && is_array($savedConfig['faq_section_ids'])) {
            $savedConfig['faq_section_ids'] = array_map('strval', $savedConfig['faq_section_ids']);
        }

        $this->config = array_merge($this->config, $savedConfig);
        $this->is_active = $landingPage->is_active;
        $this->order = $landingPage->order;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:landing_pages,slug,'.$this->landingPage->id,
            'product_id' => 'nullable|exists:products,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function updatedName(): void
    {
        if (! $this->slugManuallyEdited && ! empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(\Illuminate\Support\Str::slug($this->name));
        }
    }

    public function updatedSlug(): void
    {
        $this->slugManuallyEdited = true;
        if (! empty($this->slug)) {
            $normalizedSlug = \Illuminate\Support\Str::slug($this->slug);
            // Only update if it's different (to avoid unnecessary updates)
            if ($normalizedSlug !== $this->slug) {
                $this->slug = $normalizedSlug;
            }
        }
    }

    protected function generateUniqueSlug(string $baseSlug): string
    {
        if (empty($baseSlug)) {
            return '';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (LandingPageConfig::where('slug', $slug)->where('id', '!=', $this->landingPage->id)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function update(): void
    {
        // Ensure slug is unique before validation
        if (! empty($this->slug)) {
            $this->slug = $this->generateUniqueSlug(\Illuminate\Support\Str::slug($this->slug));
        } elseif (! empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(\Illuminate\Support\Str::slug($this->name));
        }

        $this->validate();

        // Convert string IDs back to integers for storage
        $config = $this->config;
        if (isset($config['features_section_ids']) && is_array($config['features_section_ids'])) {
            $config['features_section_ids'] = array_map('intval', $config['features_section_ids']);
        }
        if (isset($config['testimonial_ids']) && is_array($config['testimonial_ids'])) {
            $config['testimonial_ids'] = array_map('intval', $config['testimonial_ids']);
        }
        if (isset($config['faq_section_ids']) && is_array($config['faq_section_ids'])) {
            $config['faq_section_ids'] = array_map('intval', $config['faq_section_ids']);
        }

        $this->landingPage->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'product_id' => $this->product_id ?: null,
            'meta_title' => $this->meta_title ?: null,
            'meta_description' => $this->meta_description ?: null,
            'config' => $config,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ]);

        Cache::forget(Tenancy::cacheKey('landing.page.'.$this->slug));

        session()->flash('message', __('Landing page updated successfully.'));

        $this->redirect(route('admin.landing-pages.index'));
    }

    public function render()
    {
        $featureSections = LandingPageSection::where('type', 'features')
            ->where('is_active', true)
            ->when($this->featureSectionSearch, function ($query) {
                $query->where('title_en', 'like', '%'.$this->featureSectionSearch.'%')
                    ->orWhere('title_bn', 'like', '%'.$this->featureSectionSearch.'%');
            })
            ->orderBy('order')
            ->get();

        $testimonials = Testimonial::where('is_active', true)
            ->when($this->testimonialSearch, function ($query) {
                $query->where('name', 'like', '%'.$this->testimonialSearch.'%')
                    ->orWhere('content', 'like', '%'.$this->testimonialSearch.'%');
            })
            ->orderBy('order')
            ->get();

        $faqSections = LandingPageSection::where('type', 'faq')
            ->where('is_active', true)
            ->when($this->faqSectionSearch, function ($query) {
                $query->where('title_en', 'like', '%'.$this->faqSectionSearch.'%')
                    ->orWhere('title_bn', 'like', '%'.$this->faqSectionSearch.'%');
            })
            ->orderBy('order')
            ->get();

        return view('livewire.admin.landing-pages.edit', [
            'products' => Product::where('is_active', true)->orderBy('name_en')->get(),
            'featureSections' => $featureSections,
            'faqSections' => $faqSections,
            'testimonials' => $testimonials,
        ])->layout('components.layouts.app', [
            'title' => __('Edit Landing Page'),
        ]);
    }
}
