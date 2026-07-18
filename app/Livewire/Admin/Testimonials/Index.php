<?php

namespace App\Livewire\Admin\Testimonials;

use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterRating = '';

    public $perPage = 15;

    public $sortField = 'order';

    public $sortDirection = 'asc';

    public $showModal = false;

    public $editingId = null;

    public $name = '';

    public $location = '';

    public $content_en = '';

    public $content_bn = '';

    public $image;

    public $current_image;

    public $rating = 5;

    public $is_active = true;

    public $order = 0;

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterStatus', 'filterRating', 'perPage', 'sortField', 'sortDirection'];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'content_en' => 'required|string',
            'content_bn' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRating(): void
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
            $this->selectedItems = $this->getTestimonialsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function toggleStatus($testimonialId): void
    {
        $testimonial = Testimonial::findOrFail($testimonialId);
        $testimonial->update(['is_active' => ! $testimonial->is_active]);
        $this->clearCache();
        session()->flash('message', __('Testimonial status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$testimonialId]);
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one testimonial.'));

            return;
        }

        $testimonials = Testimonial::whereIn('id', $this->selectedItems)->get();
        $newStatus = $testimonials->first()->is_active ? false : true;

        foreach ($testimonials as $testimonial) {
            $testimonial->update(['is_active' => $newStatus]);
        }

        $this->clearCache();
        session()->flash('message', __(':count testimonial(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one testimonial.'));

            return;
        }

        $testimonials = Testimonial::whereIn('id', $this->selectedItems)->get();
        foreach ($testimonials as $testimonial) {
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
        }

        Testimonial::whereIn('id', $this->selectedItems)->delete();
        $this->clearCache();
        session()->flash('message', __(':count testimonial(s) deleted successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function duplicateTestimonial($testimonialId): void
    {
        $original = Testimonial::findOrFail($testimonialId);

        $newTestimonial = $original->replicate();
        $newTestimonial->name = $original->name.' (Copy)';
        $newTestimonial->is_active = false;
        $maxOrder = Testimonial::max('order');
        $newTestimonial->order = $maxOrder ? $maxOrder + 1 : 0;
        $newTestimonial->image = null; // Don't duplicate image
        $newTestimonial->save();

        $this->clearCache();
        session()->flash('message', __('Testimonial duplicated successfully.'));
    }

    protected function clearCache(): void
    {
        Cache::forget('testimonials.active');
    }

    public function createTestimonial(): void
    {
        $this->reset(['editingId', 'name', 'location', 'content_en', 'content_bn', 'image', 'current_image', 'rating', 'is_active', 'order']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function editTestimonial(Testimonial $testimonial): void
    {
        $this->editingId = $testimonial->id;
        $this->name = $testimonial->name;
        $this->location = $testimonial->location;
        $this->content_en = $testimonial->content_en;
        $this->content_bn = $testimonial->content_bn;
        $this->current_image = $testimonial->image;
        $this->image = null;
        $this->rating = $testimonial->rating;
        $this->is_active = $testimonial->is_active;
        $this->order = $testimonial->order;
        $this->showModal = true;
    }

    public function storeTestimonial(): void
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('testimonials', 'public');
        }

        Testimonial::create([
            'name' => $this->name,
            'location' => $this->location,
            'content_en' => $this->content_en,
            'content_bn' => $this->content_bn,
            'image' => $imagePath,
            'rating' => $this->rating,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ]);

        $this->clearCache();
        session()->flash('message', __('Testimonial created successfully.'));
        $this->showModal = false;
    }

    public function updateTestimonial(): void
    {
        $this->validate();

        $testimonial = Testimonial::findOrFail($this->editingId);

        $imagePath = $this->current_image;
        if ($this->image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->image->store('testimonials', 'public');
        }

        $testimonial->update([
            'name' => $this->name,
            'location' => $this->location,
            'content_en' => $this->content_en,
            'content_bn' => $this->content_bn,
            'image' => $imagePath,
            'rating' => $this->rating,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ]);

        $this->clearCache();
        session()->flash('message', __('Testimonial updated successfully.'));
        $this->showModal = false;
    }

    public function deleteTestimonial($testimonialId): void
    {
        $testimonial = Testimonial::findOrFail($testimonialId);
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->delete();
        $this->clearCache();
        session()->flash('message', __('Testimonial deleted successfully.'));
    }

    public function updateOrder(array $testimonialIds): void
    {
        if (empty($testimonialIds)) {
            return;
        }

        // Get all testimonials to find their original positions
        $allTestimonials = Testimonial::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('location', 'like', '%'.$this->search.'%')
                    ->orWhere('content_en', 'like', '%'.$this->search.'%')
                    ->orWhere('content_bn', 'like', '%'.$this->search.'%');
            })
            ->orderBy('order')
            ->pluck('id')
            ->toArray();

        // Find the range of the reordered testimonials in the full list
        $firstReorderedIndex = array_search($testimonialIds[0], $allTestimonials);
        $lastReorderedIndex = array_search(end($testimonialIds), $allTestimonials);

        if ($firstReorderedIndex === false) {
            $firstReorderedIndex = 0;
        }
        if ($lastReorderedIndex === false) {
            $lastReorderedIndex = count($allTestimonials) - 1;
        }

        // Split the testimonials: before, reordered, after
        $before = array_slice($allTestimonials, 0, $firstReorderedIndex);
        $after = array_slice($allTestimonials, $lastReorderedIndex + 1);

        // Reconstruct the full list with reordered testimonials
        $reorderedList = array_merge($before, $testimonialIds, $after);

        // Update all testimonials with new order
        foreach ($reorderedList as $order => $testimonialId) {
            Testimonial::where('id', $testimonialId)->update(['order' => $order]);
        }

        $this->clearCache();
        session()->flash('message', __('Testimonial order updated successfully.'));
    }

    protected function getTestimonialsQuery()
    {
        return Testimonial::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('location', 'like', '%'.$this->search.'%')
                    ->orWhere('content_en', 'like', '%'.$this->search.'%')
                    ->orWhere('content_bn', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterRating !== '', function ($query) {
                $query->where('rating', $this->filterRating);
            });
    }

    public function render()
    {
        $testimonials = $this->getTestimonialsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'inactive' => Testimonial::where('is_active', false)->count(),
            'average_rating' => round(Testimonial::where('is_active', true)->avg('rating') ?? 0, 1),
            'by_rating' => Testimonial::selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->get()
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        return view('livewire.admin.testimonials.index', [
            'testimonials' => $testimonials,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Testimonials'),
        ]);
    }
}
