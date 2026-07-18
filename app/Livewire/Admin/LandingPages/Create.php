<?php

namespace App\Livewire\Admin\LandingPages;

use App\Models\LandingPageConfig;
use App\Models\LandingPageSection;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Create extends Component
{
    public $name = '';

    public $slug = '';

    public $slugManuallyEdited = false;

    public $product_id = null;

    public $meta_title = '';

    public $meta_description = '';

    public $config = [
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

    public $duplicateFrom = null;

    public $featureSectionSearch = '';

    public $testimonialSearch = '';

    public $faqSectionSearch = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:landing_pages,slug',
        'product_id' => 'nullable|exists:products,id',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'is_active' => 'boolean',
        'order' => 'integer|min:0',
    ];

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

    public function duplicateFromExisting(): void
    {
        if (! $this->duplicateFrom) {
            return;
        }

        $source = LandingPageConfig::findOrFail($this->duplicateFrom);

        $this->name = $source->name.' (Copy)';
        $this->slug = $this->generateUniqueSlug($source->slug.'-copy');
        $this->slugManuallyEdited = false;
        $this->product_id = $source->product_id;
        $this->meta_title = $source->meta_title;
        $this->meta_description = $source->meta_description;

        $sourceConfig = $source->config ?? [];
        // Convert integer IDs to strings for Livewire checkboxes
        if (isset($sourceConfig['features_section_ids']) && is_array($sourceConfig['features_section_ids'])) {
            $sourceConfig['features_section_ids'] = array_map('strval', $sourceConfig['features_section_ids']);
        }
        if (isset($sourceConfig['testimonial_ids']) && is_array($sourceConfig['testimonial_ids'])) {
            $sourceConfig['testimonial_ids'] = array_map('strval', $sourceConfig['testimonial_ids']);
        }
        if (isset($sourceConfig['faq_section_ids']) && is_array($sourceConfig['faq_section_ids'])) {
            $sourceConfig['faq_section_ids'] = array_map('strval', $sourceConfig['faq_section_ids']);
        }

        $this->config = array_merge($this->config, $sourceConfig);
        $this->is_active = false;
        $this->order = $source->order;
    }

    protected function generateUniqueSlug(string $baseSlug): string
    {
        if (empty($baseSlug)) {
            return '';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (LandingPageConfig::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function save(): void
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

        LandingPageConfig::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'product_id' => $this->product_id ?: null,
            'meta_title' => $this->meta_title ?: null,
            'meta_description' => $this->meta_description ?: null,
            'config' => $config,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ]);

        Cache::forget('landing.page.'.$this->slug);

        session()->flash('message', __('Landing page created successfully.'));

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

        return view('livewire.admin.landing-pages.create', [
            'products' => Product::where('is_active', true)->orderBy('name_en')->get(),
            'featureSections' => $featureSections,
            'faqSections' => $faqSections,
            'testimonials' => $testimonials,
            'existingLandingPages' => LandingPageConfig::orderBy('name')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('Create Landing Page'),
        ]);
    }
}
