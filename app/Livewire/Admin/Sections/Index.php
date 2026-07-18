<?php

namespace App\Livewire\Admin\Sections;

use App\Models\LandingPageSection;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $editingSectionId = null;

    public $showModal = false;

    public $type;

    public $title_en;

    public $title_bn;

    public $content_en;

    public $content_bn;

    public $data = [];

    public $image;

    public $current_image;

    public $order;

    public $is_active;

    public $filterType = '';

    public $filterStatus = '';

    public $search = '';

    public $perPage = 10;

    public $sortField = 'order';

    public $sortDirection = 'asc';

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterType', 'filterStatus', 'perPage', 'sortField', 'sortDirection'];

    protected function rules(): array
    {
        return [
            'type' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'content_en' => 'nullable|string',
            'content_bn' => 'nullable|string',
            'data' => 'nullable|array',
            'image' => 'nullable|image|max:1024',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function mount(): void
    {
        //
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedItems = $this->getSectionsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function toggleStatus($sectionId): void
    {
        $section = LandingPageSection::findOrFail($sectionId);
        $section->update(['is_active' => ! $section->is_active]);
        $this->clearCache();
        session()->flash('message', __('Section status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$sectionId]);
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one section.'));

            return;
        }

        $sections = LandingPageSection::whereIn('id', $this->selectedItems)->get();
        $newStatus = $sections->first()->is_active ? false : true;

        foreach ($sections as $section) {
            $section->update(['is_active' => $newStatus]);
        }

        $this->clearCache();
        session()->flash('message', __(':count section(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one section.'));

            return;
        }

        $sections = LandingPageSection::whereIn('id', $this->selectedItems)->get();
        foreach ($sections as $section) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
        }

        LandingPageSection::whereIn('id', $this->selectedItems)->delete();
        $this->clearCache();
        session()->flash('message', __(':count section(s) deleted successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function deleteSection($sectionId): void
    {
        $section = LandingPageSection::findOrFail($sectionId);
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }
        $section->delete();
        $this->clearCache();
        session()->flash('message', __('Section deleted successfully.'));
    }

    public function duplicateSection($sectionId): void
    {
        $original = LandingPageSection::findOrFail($sectionId);

        $newSection = $original->replicate();
        $newSection->title_en = ($original->title_en ? $original->title_en.' (Copy)' : null);
        $newSection->title_bn = ($original->title_bn ? $original->title_bn.' (Copy)' : null);
        $newSection->is_active = false;
        $maxOrder = LandingPageSection::max('order');
        $newSection->order = $maxOrder ? $maxOrder + 1 : 0;
        $newSection->image = null; // Don't duplicate image
        $newSection->save();

        $this->clearCache();
        session()->flash('message', __('Section duplicated successfully.'));
    }

    protected function clearCache(): void
    {
        Cache::forget(Tenancy::cacheKey('landing.sections.hero'));
        Cache::forget(Tenancy::cacheKey('landing.sections.features'));
        Cache::forget(Tenancy::cacheKey('landing.sections.faq'));
        Cache::forget(Tenancy::cacheKey('landing.sections.testimonials'));
    }

    public function editSection($sectionId = null): void
    {
        $this->reset(['editingSectionId', 'type', 'title_en', 'title_bn', 'content_en', 'content_bn', 'data', 'image', 'current_image', 'order', 'is_active']);

        if ($sectionId) {
            $section = LandingPageSection::findOrFail($sectionId);
            $this->editingSectionId = $section->id;
            $this->type = $section->type;
            $this->title_en = $section->title_en;
            $this->title_bn = $section->title_bn;
            $this->content_en = $section->content_en;
            $this->content_bn = $section->content_bn;
            $this->data = $section->data ?? [];
            $this->current_image = $section->image;
            $this->image = null;
            $this->order = $section->order;
            $this->is_active = $section->is_active;
        } else {
            // Set defaults for new section
            $this->is_active = true;
            $maxOrder = LandingPageSection::max('order');
            $this->order = $maxOrder ? $maxOrder + 1 : 0;
        }

        $this->showModal = true;
    }

    public function saveSection(): void
    {
        $this->validate();

        $imagePath = $this->current_image;
        if ($this->image) {
            if ($imagePath && $this->editingSectionId) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->image->store(Tenancy::storagePath('sections'), 'public');
        } elseif (! $this->editingSectionId) {
            // New section with no image uploaded
            $imagePath = null;
        }

        $data = [
            'type' => $this->type,
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'content_en' => $this->content_en,
            'content_bn' => $this->content_bn,
            'data' => $this->data ?? [],
            'image' => $imagePath,
            'order' => $this->order ?? 0,
            'is_active' => $this->is_active ?? true,
        ];

        if ($this->editingSectionId) {
            $section = LandingPageSection::findOrFail($this->editingSectionId);
            $section->update($data);
            $message = __('Section updated successfully.');
        } else {
            LandingPageSection::create($data);
            $message = __('Section created successfully.');
        }

        $this->clearCache();

        session()->flash('message', $message);
        $this->reset(['editingSectionId', 'type', 'title_en', 'title_bn', 'content_en', 'content_bn', 'data', 'image', 'current_image', 'order', 'is_active']);
        $this->showModal = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function updateSection(): void
    {
        $this->saveSection();
    }

    public function getSectionTypesProperty(): array
    {
        return [
            'hero' => __('Hero Section'),
            'features' => __('Features Section'),
            'testimonials' => __('Testimonials Section'),
            'faq' => __('FAQ Section'),
            'about' => __('About Section'),
            'contact' => __('Contact Section'),
            'products' => __('Products Section'),
            'benefits' => __('Benefits Section'),
            'cta' => __('Call to Action Section'),
        ];
    }

    public function updateOrder(array $sectionIds): void
    {
        if (empty($sectionIds)) {
            return;
        }

        // Get all sections (not paginated) of the filtered type to find their original positions
        $allSections = LandingPageSection::query()
            ->when($this->filterType, fn ($query) => $query->where('type', $this->filterType))
            ->orderBy('order')
            ->pluck('id')
            ->toArray();

        // Find the range of the reordered sections in the full list
        $firstReorderedIndex = array_search($sectionIds[0], $allSections);
        $lastReorderedIndex = array_search(end($sectionIds), $allSections);

        if ($firstReorderedIndex === false) {
            $firstReorderedIndex = 0;
        }
        if ($lastReorderedIndex === false) {
            $lastReorderedIndex = count($allSections) - 1;
        }

        // Split the sections: before, reordered, after
        $before = array_slice($allSections, 0, $firstReorderedIndex);
        $after = array_slice($allSections, $lastReorderedIndex + 1);

        // Reconstruct the full list with reordered sections
        $reorderedList = array_merge($before, $sectionIds, $after);

        // Update all sections with new order
        foreach ($reorderedList as $order => $sectionId) {
            LandingPageSection::where('id', $sectionId)->update(['order' => $order]);
        }

        $this->clearCache();

        session()->flash('message', __('Section order updated successfully.'));
    }

    protected function getSectionsQuery()
    {
        return LandingPageSection::query()
            ->when($this->search, function ($query) {
                $query->where('title_en', 'like', '%'.$this->search.'%')
                    ->orWhere('title_bn', 'like', '%'.$this->search.'%')
                    ->orWhere('content_en', 'like', '%'.$this->search.'%')
                    ->orWhere('content_bn', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterType !== '', function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            });
    }

    public function render()
    {
        $sections = $this->getSectionsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => LandingPageSection::count(),
            'active' => LandingPageSection::where('is_active', true)->count(),
            'inactive' => LandingPageSection::where('is_active', false)->count(),
            'by_type' => LandingPageSection::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
        ];

        return view('livewire.admin.sections.index', [
            'sections' => $sections,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Landing Page Sections'),
        ]);
    }
}
