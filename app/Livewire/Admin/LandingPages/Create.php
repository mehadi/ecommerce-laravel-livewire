<?php

namespace App\Livewire\Admin\LandingPages;

use App\Models\LandingPageConfig;
use App\Models\LandingPageSection;
use App\Models\Product;
use App\Models\Testimonial;
use App\Support\Tenancy;
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

    public $heroTitle = '';

    public $heroContent = '';

    public $heroBadgeText = '';

    /**
     * Ordered, toggleable page blocks (everything after the pinned hero).
     */
    public array $blocks = [];

    public $is_active = true;

    public $order = 0;

    public $duplicateFrom = null;

    public $featureSectionSearch = '';

    public $testimonialSearch = '';

    public $faqSectionSearch = '';

    public $aboutSectionSearch = '';

    public $benefitsSectionSearch = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:landing_pages,slug',
        'product_id' => 'nullable|exists:products,id',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'is_active' => 'boolean',
        'order' => 'integer|min:0',
    ];

    public function mount(): void
    {
        $this->blocks = LandingPageConfig::defaultBlocks();
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
        $this->heroTitle = $sourceConfig['hero_title'] ?? '';
        $this->heroContent = $sourceConfig['hero_content'] ?? '';
        $this->heroBadgeText = $sourceConfig['hero_badge_text'] ?? '';

        // Convert integer IDs to strings for Livewire checkboxes.
        $this->blocks = array_map(function (array $block) {
            foreach (['section_ids', 'testimonial_ids'] as $idsKey) {
                if (isset($block[$idsKey]) && is_array($block[$idsKey])) {
                    $block[$idsKey] = array_map('strval', $block[$idsKey]);
                }
            }

            return $block;
        }, $source->normalizedBlocks());

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

    /**
     * Called from the admin's drag-and-drop panel with the block types in
     * their new order (each type appears exactly once in $this->blocks).
     */
    public function updateBlockOrder(array $types): void
    {
        $blocksByType = collect($this->blocks)->keyBy('type');

        $this->blocks = collect($types)
            ->map(fn ($type) => $blocksByType->get($type))
            ->filter()
            ->values()
            ->all();
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
        $blocks = array_map(function (array $block) {
            foreach (['section_ids', 'testimonial_ids'] as $idsKey) {
                if (isset($block[$idsKey]) && is_array($block[$idsKey])) {
                    $block[$idsKey] = array_map('intval', $block[$idsKey]);
                }
            }

            return $block;
        }, $this->blocks);

        LandingPageConfig::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'product_id' => $this->product_id ?: null,
            'meta_title' => $this->meta_title ?: null,
            'meta_description' => $this->meta_description ?: null,
            'config' => [
                'hero_title' => $this->heroTitle,
                'hero_content' => $this->heroContent,
                'hero_badge_text' => $this->heroBadgeText,
                'blocks' => $blocks,
            ],
            'is_active' => $this->is_active,
            'order' => $this->order,
        ]);

        Cache::forget(Tenancy::cacheKey('landing.page.'.$this->slug));

        session()->flash('message', __('Landing page created successfully.'));

        $this->redirect(route('admin.landing-pages.index'));
    }

    public function render()
    {
        $sectionsOfType = function (string $type, string $search) {
            return LandingPageSection::where('type', $type)
                ->where('is_active', true)
                ->when($search, function ($query) use ($search) {
                    $query->where('title_en', 'like', '%'.$search.'%')
                        ->orWhere('title_bn', 'like', '%'.$search.'%');
                })
                ->orderBy('order')
                ->get();
        };

        return view('livewire.admin.landing-pages.create', [
            'products' => Product::where('is_active', true)->orderBy('name_en')->get(),
            'featureSections' => $sectionsOfType('features', $this->featureSectionSearch),
            'faqSections' => $sectionsOfType('faq', $this->faqSectionSearch),
            'aboutSections' => $sectionsOfType('about', $this->aboutSectionSearch),
            'benefitsSections' => $sectionsOfType('benefits', $this->benefitsSectionSearch),
            'testimonials' => Testimonial::where('is_active', true)
                ->when($this->testimonialSearch, function ($query) {
                    $query->where('name', 'like', '%'.$this->testimonialSearch.'%')
                        ->orWhere('content', 'like', '%'.$this->testimonialSearch.'%');
                })
                ->orderBy('order')
                ->get(),
            'existingLandingPages' => LandingPageConfig::orderBy('name')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('Create Landing Page'),
        ]);
    }
}
