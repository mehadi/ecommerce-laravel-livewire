<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;

    public $editingId = null;

    public $parent_id = null;

    public $name_en = '';

    public $name_bn = '';

    public $slug = '';

    public $description_en = '';

    public $description_bn = '';

    public $search = '';

    public $filterStatus = '';

    public $filterParent = '';

    public $filterHasSubcategories = '';

    public $perPage = 15;

    public $sortField = 'order';

    public $sortDirection = 'asc';

    public $image;

    public $current_image;

    public $is_active = true;

    public $order = 0;

    public $slugAvailable = null;

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterStatus', 'filterParent', 'filterHasSubcategories', 'perPage', 'sortField', 'sortDirection'];

    protected $rules = [
        'parent_id' => 'nullable|exists:categories,id',
        'name_en' => 'required|string|max:255',
        'name_bn' => 'nullable|string|max:255',
        'slug' => 'required|string|unique:categories,slug',
        'description_en' => 'nullable|string',
        'description_bn' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
        'order' => 'integer|min:0',
    ];

    public function openModal($id = null): void
    {
        $this->reset(['editingId', 'parent_id', 'name_en', 'name_bn', 'slug', 'description_en', 'description_bn', 'image', 'current_image', 'is_active', 'order', 'slugAvailable']);
        if ($id) {
            $category = Category::find($id);
            $this->editingId = $id;
            $this->parent_id = $category->parent_id;
            $this->name_en = $category->name_en;
            $this->name_bn = $category->name_bn;
            $this->slug = $category->slug;
            $this->description_en = $category->description_en;
            $this->description_bn = $category->description_bn;
            $this->current_image = $category->image;
            $this->is_active = $category->is_active;
            $this->order = $category->order;
            $this->slugAvailable = true;
        }
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function removeImage(): void
    {
        if ($this->current_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->current_image);
        }

        $this->image = null;
        $this->current_image = null;
    }

    public function updatedNameEn(): void
    {
        if (! $this->editingId && $this->name_en) {
            $this->slug = \Illuminate\Support\Str::slug($this->name_en);
            $this->checkSlugAvailability();
        }
    }

    public function updatedSlug(): void
    {
        $this->checkSlugAvailability();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterParent(): void
    {
        $this->resetPage();
    }

    public function updatingFilterHasSubcategories(): void
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
            $this->selectedItems = $this->getCategoriesQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function toggleStatus($categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        $category->update(['is_active' => ! $category->is_active]);
        Cache::forget(Tenancy::cacheKey('categories.all'));
        session()->flash('message', __('Category status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$categoryId]);
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one category.'));

            return;
        }

        $categories = Category::whereIn('id', $this->selectedItems)->get();
        $newStatus = $categories->first()->is_active ? false : true;

        foreach ($categories as $category) {
            $category->update(['is_active' => $newStatus]);
        }

        Cache::forget(Tenancy::cacheKey('categories.all'));
        session()->flash('message', __(':count category(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one category.'));

            return;
        }

        $categories = Category::whereIn('id', $this->selectedItems)->get();
        $deletedCount = 0;
        $errorCount = 0;

        foreach ($categories as $category) {
            if ($category->children()->count() > 0) {
                $errorCount++;

                continue;
            }
            $category->delete();
            $deletedCount++;
        }

        Cache::forget(Tenancy::cacheKey('categories.all'));

        if ($deletedCount > 0) {
            session()->flash('message', __(':count category(s) deleted successfully.', ['count' => $deletedCount]));
        }

        if ($errorCount > 0) {
            session()->flash('error', __(':count category(s) could not be deleted because they have subcategories.', ['count' => $errorCount]));
        }

        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function duplicate($categoryId): void
    {
        $original = Category::findOrFail($categoryId);

        $newCategory = $original->replicate();
        $newCategory->name_en = $original->name_en.' (Copy)';
        $newCategory->name_bn = $original->name_bn ? $original->name_bn.' (Copy)' : null;
        $newCategory->slug = $this->generateUniqueSlug($original->slug.'-copy');
        $newCategory->is_active = false;
        $newCategory->save();

        Cache::forget(Tenancy::cacheKey('categories.all'));
        session()->flash('message', __('Category duplicated successfully.'));
    }

    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function getCategoriesQuery()
    {
        return Category::with(['parent', 'children'])
            ->withCount(['products', 'children'])
            ->when($this->search, function ($query) {
                $query->where('name_en', 'like', '%'.$this->search.'%')
                    ->orWhere('name_bn', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterParent !== '', function ($query) {
                if ($this->filterParent === 'none') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $this->filterParent);
                }
            })
            ->when($this->filterHasSubcategories !== '', function ($query) {
                if ($this->filterHasSubcategories === 'yes') {
                    $query->has('children');
                } else {
                    $query->doesntHave('children');
                }
            });
    }

    public function checkSlugAvailability(): void
    {
        $slug = trim($this->slug);

        if (empty($slug)) {
            $this->slugAvailable = null;

            return;
        }

        $normalizedSlug = \Illuminate\Support\Str::slug($slug);

        $query = Category::where('slug', $normalizedSlug);

        if ($this->editingId) {
            $query->where('id', '!=', $this->editingId);
        }

        $this->slugAvailable = ! $query->exists();
    }

    public function generateSlug(): void
    {
        if ($this->name_en) {
            $this->slug = \Illuminate\Support\Str::slug($this->name_en);
            $this->checkSlugAvailability();
        }
    }

    public function save(): void
    {
        if ($this->editingId) {
            $this->rules['slug'] = 'required|string|unique:categories,slug,'.$this->editingId;
            $this->rules['parent_id'] = 'nullable|exists:categories,id|not_in:'.$this->editingId;
        } else {
            $this->rules['slug'] = 'required|string|unique:categories,slug';
        }

        $this->validate();

        if ($this->editingId && $this->parent_id) {
            $descendantIds = $this->getDescendantIds((int) $this->editingId, Category::all());
            if (in_array((int) $this->parent_id, $descendantIds)) {
                $this->addError('parent_id', __('Cannot select a subcategory of this category as its parent.'));

                return;
            }
        }

        $slug = \Illuminate\Support\Str::slug($this->slug);

        $data = [
            'parent_id' => $this->parent_id,
            'name_en' => $this->name_en,
            'name_bn' => $this->name_bn,
            'slug' => $slug,
            'description_en' => $this->description_en,
            'description_bn' => $this->description_bn,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->image) {
            if ($this->current_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->current_image);
            }
            $data['image'] = $this->image->store(Tenancy::storagePath('categories'), 'public');
        } elseif ($this->editingId) {
            $data['image'] = $this->current_image;
        }

        if ($this->editingId) {
            Category::find($this->editingId)->update($data);
            session()->flash('message', 'Category updated successfully.');
        } else {
            Category::create($data);
            session()->flash('message', 'Category created successfully.');
        }

        Cache::forget(Tenancy::cacheKey('categories.all'));
        $this->closeModal();
    }

    public function delete($id): void
    {
        $category = Category::find($id);
        if ($category->children()->count() > 0) {
            session()->flash('error', 'Cannot delete category with subcategories. Please delete subcategories first.');

            return;
        }
        $category->delete();
        Cache::forget(Tenancy::cacheKey('categories.all'));
        session()->flash('message', 'Category deleted successfully.');
    }

    public function getParentCategoriesProperty()
    {
        $all = Category::orderBy('order')->orderBy('name_en')->get();

        $excludeIds = [];
        if ($this->editingId) {
            $excludeIds = $this->getDescendantIds((int) $this->editingId, $all);
            $excludeIds[] = (int) $this->editingId;
        }

        return $this->flattenTree($all, null)
            ->reject(fn ($item) => in_array($item['category']->id, $excludeIds))
            ->values();
    }

    public function getCategoryTreeProperty()
    {
        $all = Category::orderBy('order')->orderBy('name_en')->get();

        return $this->buildTree($all, null);
    }

    private function buildTree($all, ?int $parentId)
    {
        return $all->where('parent_id', $parentId)->map(function ($category) use ($all) {
            $category->setRelation('children', $this->buildTree($all, $category->id));

            return $category;
        })->values();
    }

    private function flattenTree($all, ?int $parentId, int $depth = 0)
    {
        $result = collect();

        foreach ($all->where('parent_id', $parentId) as $category) {
            $result->push(['category' => $category, 'depth' => $depth]);
            $result = $result->merge($this->flattenTree($all, $category->id, $depth + 1));
        }

        return $result;
    }

    private function getDescendantIds(int $categoryId, $all): array
    {
        $ids = [];

        foreach ($all->where('parent_id', $categoryId) as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child->id, $all));
        }

        return $ids;
    }

    public function updateCategoryParent(int $categoryId, ?int $newParentId): void
    {
        $category = Category::findOrFail($categoryId);

        if ($newParentId === $category->parent_id) {
            return;
        }

        if ($newParentId !== null) {
            $newParent = Category::with('parent')->findOrFail($newParentId);

            if ($newParent->id === $category->id) {
                session()->flash('error', 'Cannot move category to itself.');

                return;
            }

            if ($this->wouldCreateCircularReference($category, $newParent)) {
                session()->flash('error', 'Cannot create circular reference.');

                return;
            }
        }

        $category->update(['parent_id' => $newParentId]);

        Cache::forget(Tenancy::cacheKey('categories.all'));

        session()->flash('message', 'Category moved successfully.');
    }

    private function wouldCreateCircularReference(Category $category, Category $newParent): bool
    {
        $currentParent = $newParent;

        while ($currentParent !== null) {
            if ($currentParent->id === $category->id) {
                return true;
            }

            if ($currentParent->relationLoaded('parent')) {
                $currentParent = $currentParent->parent;
            } else {
                if ($currentParent->parent_id === null) {
                    break;
                }
                $currentParent = Category::with('parent')->find($currentParent->parent_id);
            }
        }

        return false;
    }

    public function render()
    {
        $categories = $this->getCategoriesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'subcategories' => Category::whereNotNull('parent_id')->count(),
        ];

        $parentCategories = Category::whereNull('parent_id')->orderBy('name_en')->get();

        return view('livewire.admin.categories.index', [
            'categories' => $categories,
            'stats' => $stats,
            'parentCategories' => $parentCategories,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Categories'),
        ]);
    }
}
