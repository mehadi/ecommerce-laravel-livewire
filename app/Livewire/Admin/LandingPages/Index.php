<?php

namespace App\Livewire\Admin\LandingPages;

use App\Models\LandingPageConfig;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterProduct = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterStatus', 'filterProduct', 'perPage', 'sortField', 'sortDirection'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterProduct(): void
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
            $this->selectedItems = $this->getLandingPagesQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function toggleStatus($landingPageId): void
    {
        $landingPage = LandingPageConfig::findOrFail($landingPageId);
        $landingPage->update(['is_active' => ! $landingPage->is_active]);
        Cache::forget('landing.page.'.$landingPage->slug);
        session()->flash('message', __('Landing page status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$landingPageId]);
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one landing page.'));

            return;
        }

        $landingPages = LandingPageConfig::whereIn('id', $this->selectedItems)->get();
        $newStatus = $landingPages->first()->is_active ? false : true;

        foreach ($landingPages as $landingPage) {
            $landingPage->update(['is_active' => $newStatus]);
            Cache::forget('landing.page.'.$landingPage->slug);
        }

        session()->flash('message', __(':count landing page(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one landing page.'));

            return;
        }

        $landingPages = LandingPageConfig::whereIn('id', $this->selectedItems)->get();

        foreach ($landingPages as $landingPage) {
            Cache::forget('landing.page.'.$landingPage->slug);
            $landingPage->delete();
        }

        session()->flash('message', __(':count landing page(s) deleted successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function duplicate($landingPageId): void
    {
        $original = LandingPageConfig::findOrFail($landingPageId);

        $newLandingPage = $original->replicate();
        $newLandingPage->name = $original->name.' (Copy)';
        $newLandingPage->slug = $this->generateUniqueSlug($original->slug.'-copy');
        $newLandingPage->is_active = false;
        $newLandingPage->save();

        session()->flash('message', __('Landing page duplicated successfully.'));
        $this->redirect(route('admin.landing-pages.edit', $newLandingPage));
    }

    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (LandingPageConfig::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function deleteLandingPage($landingPageId): void
    {
        $landingPage = LandingPageConfig::findOrFail($landingPageId);
        $landingPage->delete();
        Cache::forget('landing.page.'.$landingPage->slug);
        session()->flash('message', __('Landing page deleted successfully.'));
    }

    public function delete(LandingPageConfig $landingPage): void
    {
        $this->deleteLandingPage($landingPage->id);
    }

    protected function getLandingPagesQuery()
    {
        return LandingPageConfig::query()
            ->with('product')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterProduct, function ($query) {
                $query->where('product_id', $this->filterProduct);
            });
    }

    public function render()
    {
        $landingPages = $this->getLandingPagesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => LandingPageConfig::count(),
            'active' => LandingPageConfig::where('is_active', true)->count(),
            'inactive' => LandingPageConfig::where('is_active', false)->count(),
        ];

        return view('livewire.admin.landing-pages.index', [
            'landingPages' => $landingPages,
            'stats' => $stats,
            'products' => Product::where('is_active', true)->orderBy('name_en')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('Manage Landing Pages'),
        ]);
    }
}
