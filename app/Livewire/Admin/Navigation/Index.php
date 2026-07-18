<?php

namespace App\Livewire\Admin\Navigation;

use App\Models\Category;
use App\Models\NavbarComponent;
use App\Models\NavigationItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Index extends Component
{
    public $activeZone = 'desktop';

    public $showModal = false;

    public $editingId = null;

    public $label_en = '';

    public $label_bn = '';

    public $url = '';

    public $type = 'link';

    public $route_name = '';

    public $is_active = true;

    public $open_in_new_tab = false;

    public $parent_id = null;

    public $categorySearch = '';

    public $showAllCategories = false;

    public $icon = '';

    public $search = '';

    public $selectedItems = [];

    public $selectAll = false;

    protected $rules = [
        'label_en' => 'required|string|max:255',
        'label_bn' => 'nullable|string|max:255',
        'icon' => 'nullable|string|max:50',
        'url' => 'required|string|max:255',
        'type' => 'required|in:link,section,route',
        'route_name' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function openModal($id = null): void
    {
        $this->reset(['editingId', 'label_en', 'label_bn', 'icon', 'url', 'type', 'route_name', 'is_active', 'open_in_new_tab', 'parent_id']);

        if ($id) {
            $item = NavigationItem::findOrFail($id);
            $this->editingId = $id;
            $this->label_en = $item->label_en;
            $this->label_bn = $item->label_bn;
            $this->icon = $item->icon ?? '';
            $this->url = $item->url;
            $this->type = $item->type;
            $this->route_name = $item->route_name;
            $this->is_active = $item->is_active;
            $this->open_in_new_tab = $item->open_in_new_tab;
            $this->parent_id = $item->parent_id;
        } else {
            $this->type = 'link';
            $this->is_active = true;
            $this->open_in_new_tab = false;
            $this->parent_id = null;
            $this->icon = '';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $this->validate();

        // Validate route if type is route
        if ($this->type === 'route' && $this->route_name) {
            try {
                route($this->route_name);
            } catch (\Exception $e) {
                $this->addError('route_name', __('Route does not exist. Please check the route name.'));

                return;
            }
        }

        // Check for duplicate URL (excluding current item)
        $duplicate = NavigationItem::where('url', $this->url)
            ->where('type', $this->type)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->first();

        if ($duplicate) {
            $this->addError('url', __('A navigation item with this URL already exists.'));

            return;
        }

        $data = [
            'label_en' => $this->label_en,
            'label_bn' => $this->label_bn,
            'icon' => $this->icon ?: null,
            'url' => $this->url,
            'type' => $this->type,
            'route_name' => $this->type === 'route' ? $this->route_name : null,
            'is_active' => $this->is_active,
            'open_in_new_tab' => $this->open_in_new_tab,
            'parent_id' => $this->parent_id ?: null,
        ];

        if ($this->editingId) {
            NavigationItem::findOrFail($this->editingId)->update($data);
            session()->flash('message', __('Navigation item updated successfully.'));
        } else {
            $maxOrder = NavigationItem::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            NavigationItem::create($data);
            session()->flash('message', __('Navigation item created successfully.'));
        }

        Cache::forget('navigation.items.active');
        $this->closeModal();
        $this->selectedItems = [];
    }

    public function delete($id): void
    {
        NavigationItem::findOrFail($id)->delete();
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation item deleted successfully.'));
        $this->selectedItems = [];
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('message', __('Please select items to delete.'), 'error');

            return;
        }

        NavigationItem::whereIn('id', $this->selectedItems)->delete();
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Selected navigation items deleted successfully.'));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkActivate(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('message', __('Please select items to activate.'), 'error');

            return;
        }

        NavigationItem::whereIn('id', $this->selectedItems)->update(['is_active' => true]);
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Selected navigation items activated successfully.'));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDeactivate(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('message', __('Please select items to deactivate.'), 'error');

            return;
        }

        NavigationItem::whereIn('id', $this->selectedItems)->update(['is_active' => false]);
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Selected navigation items deactivated successfully.'));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedItems = $this->getFilteredItems()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSearch(): void
    {
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updateOrder(array $itemIds): void
    {
        if (empty($itemIds)) {
            return;
        }

        foreach ($itemIds as $order => $itemId) {
            NavigationItem::where('id', $itemId)->update(['order' => $order]);
        }

        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation order updated successfully.'));
    }

    public function updateItemParent(int $itemId, ?int $parentId = null): void
    {
        $item = NavigationItem::findOrFail($itemId);

        // Prevent circular references
        if ($parentId && $parentId === $itemId) {
            session()->flash('message', __('An item cannot be its own parent.'), 'error');

            return;
        }

        // Check if parentId is a descendant of itemId (would create circular reference)
        if ($parentId) {
            $parent = NavigationItem::findOrFail($parentId);
            $currentParentId = $parent->id;
            $visited = [$itemId]; // Track visited IDs to prevent infinite loops

            while ($currentParentId) {
                if (in_array($currentParentId, $visited)) {
                    session()->flash('message', __('Cannot create circular reference in navigation hierarchy.'), 'error');

                    return;
                }
                $visited[] = $currentParentId;

                if ($currentParentId === $itemId) {
                    session()->flash('message', __('Cannot create circular reference in navigation hierarchy.'), 'error');

                    return;
                }

                $currentParent = NavigationItem::find($currentParentId);
                if (! $currentParent) {
                    break;
                }
                $currentParentId = $currentParent->parent_id;
            }
        }

        $item->update(['parent_id' => $parentId]);
        $this->reorderItemsAfterParentChange();
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation item relationship updated successfully.'));
    }

    public function updateItemOrderAndParent(int $itemId, int $targetItemId, bool $makeChild = true): void
    {
        $item = NavigationItem::findOrFail($itemId);
        $targetItem = NavigationItem::findOrFail($targetItemId);

        // Prevent circular references
        if ($makeChild && $targetItemId === $itemId) {
            session()->flash('message', __('An item cannot be its own parent.'), 'error');

            return;
        }

        // Check circular reference - ensure target is not a descendant of item
        if ($makeChild) {
            $visited = [$itemId];
            $currentParentId = $targetItem->id;

            while ($currentParentId) {
                if (in_array($currentParentId, $visited)) {
                    session()->flash('message', __('Cannot create circular reference.'), 'error');

                    return;
                }
                $visited[] = $currentParentId;

                if ($currentParentId === $itemId) {
                    session()->flash('message', __('Cannot create circular reference. Item cannot be a child of its own descendant.'), 'error');

                    return;
                }

                $currentParent = NavigationItem::find($currentParentId);
                if (! $currentParent) {
                    break;
                }
                $currentParentId = $currentParent->parent_id;
            }
        }

        // Set parent relationship
        if ($makeChild) {
            $item->update(['parent_id' => $targetItemId]);
        } else {
            // Make it a sibling (same level as target)
            $item->update(['parent_id' => $targetItem->parent_id]);
        }

        // Reorder items
        $this->reorderItemsAfterParentChange();

        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation item moved successfully.'));
    }

    /**
     * Move an item relative to a target: as a sibling immediately before/after it,
     * or nested as its child. Used by drag-and-drop, which picks the mode from
     * where the cursor is over the target row (top/bottom edge vs. the middle).
     */
    public function moveNavigationItem(int $itemId, int $targetItemId, string $position): void
    {
        if ($itemId === $targetItemId || ! in_array($position, ['before', 'after', 'child'])) {
            return;
        }

        if ($position === 'child') {
            $this->updateItemOrderAndParent($itemId, $targetItemId, true);

            return;
        }

        $item = NavigationItem::findOrFail($itemId);
        $target = NavigationItem::findOrFail($targetItemId);
        $newParentId = $target->parent_id;

        // Prevent the item becoming a sibling of one of its own descendants.
        if ($newParentId) {
            $visited = [$itemId];
            $currentParentId = $newParentId;

            while ($currentParentId) {
                if ($currentParentId === $itemId || in_array($currentParentId, $visited)) {
                    session()->flash('message', __('Cannot create circular reference in navigation hierarchy.'), 'error');

                    return;
                }
                $visited[] = $currentParentId;
                $currentParent = NavigationItem::find($currentParentId);
                $currentParentId = $currentParent?->parent_id;
            }
        }

        $item->update(['parent_id' => $newParentId]);

        $siblingIds = NavigationItem::where('parent_id', $newParentId)
            ->where('id', '!=', $itemId)
            ->orderBy('order')
            ->pluck('id')
            ->all();

        $targetIndex = array_search($targetItemId, $siblingIds);
        $insertAt = $position === 'before' ? $targetIndex : $targetIndex + 1;
        array_splice($siblingIds, $insertAt, 0, [$itemId]);

        foreach ($siblingIds as $order => $id) {
            NavigationItem::where('id', $id)->update(['order' => $order]);
        }

        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation item moved successfully.'));
    }

    private function reorderItemsAfterParentChange(): void
    {
        // Organize items hierarchically and assign order
        $allItems = NavigationItem::all();
        $parentItems = $allItems->whereNull('parent_id')->sortBy('order');
        $childItems = $allItems->whereNotNull('parent_id');

        $order = 0;

        // First, assign order to parent items
        foreach ($parentItems as $parent) {
            $parent->update(['order' => $order++]);

            // Then assign order to its children
            $children = $childItems->where('parent_id', $parent->id)->sortBy('order');
            foreach ($children as $child) {
                $child->update(['order' => $order++]);
            }
        }

        // Handle any orphaned children
        $parentIds = $allItems->pluck('id')->toArray();
        $orphaned = $childItems->filter(function ($item) use ($parentIds) {
            return ! in_array($item->parent_id, $parentIds);
        })->sortBy('order');

        foreach ($orphaned as $orphan) {
            $orphan->update(['order' => $order++]);
        }
    }

    public function toggleStatus($id): void
    {
        $item = NavigationItem::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation item status updated successfully.'));
    }

    public function clearCache(): void
    {
        Cache::forget('navigation.items.active');
        session()->flash('message', __('Navigation cache cleared successfully. Changes will be visible on the homepage immediately.'));
    }

    public function setActiveZone(string $zone): void
    {
        $this->activeZone = in_array($zone, ['desktop', 'mobile']) ? $zone : 'desktop';
    }

    public function updateComponentOrder(array $componentIds, string $zone): void
    {
        if (empty($componentIds) || ! in_array($zone, ['desktop', 'mobile'])) {
            return;
        }

        foreach ($componentIds as $order => $componentId) {
            NavbarComponent::where('id', $componentId)->update(["order_{$zone}" => $order]);
        }

        $this->forgetNavbarCache();
        session()->flash('message', __('Navbar layout order updated successfully.'));
    }

    public function moveComponentToRegion(int $componentId, string $region, array $orderedIds): void
    {
        if (! in_array($region, ['start', 'middle', 'end']) || empty($orderedIds)) {
            return;
        }

        NavbarComponent::where('id', $componentId)->update(['zone_desktop' => $region]);

        foreach ($orderedIds as $order => $id) {
            NavbarComponent::where('id', $id)->update(['order_desktop' => $order]);
        }

        $this->forgetNavbarCache();
        session()->flash('message', __('Navbar layout updated successfully.'));
    }

    public function toggleComponentVisibility(int $id, string $zone): void
    {
        if (! in_array($zone, ['desktop', 'mobile'])) {
            return;
        }

        $component = NavbarComponent::findOrFail($id);
        $component->update(["is_visible_{$zone}" => ! $component->{"is_visible_{$zone}"}]);

        $this->forgetNavbarCache();
    }

    public function updateComponentSpan(int $id, string $zone, int $delta): void
    {
        if (! in_array($zone, ['desktop', 'mobile'])) {
            return;
        }

        $component = NavbarComponent::findOrFail($id);
        $newSpan = max(1, min(12, $component->{"span_{$zone}"} + $delta));
        $component->update(["span_{$zone}" => $newSpan]);

        $this->forgetNavbarCache();
    }

    private function forgetNavbarCache(): void
    {
        Cache::forget('navbar.components.desktop');
        Cache::forget('navbar.components.mobile');
    }

    public function addCategoryToNavigation($categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        // Check if category is already added as a navigation item
        $existingItem = NavigationItem::where('url', '/category/'.$category->slug)->first();
        if ($existingItem) {
            session()->flash('message', __('Category is already in navigation.'));

            return;
        }

        $maxOrder = NavigationItem::max('order') ?? 0;
        NavigationItem::create([
            'label_en' => $category->name_en,
            'label_bn' => $category->name_bn,
            'url' => '/category/'.$category->slug,
            'type' => 'link',
            'order' => $maxOrder + 1,
            'is_active' => true,
            'open_in_new_tab' => false,
        ]);

        Cache::forget('navigation.items.active');
        session()->flash('message', __('Category added to navigation successfully.'));
    }

    private function getFilteredItems()
    {
        $query = NavigationItem::with('parent', 'children')->orderBy('order');

        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(label_en) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(label_bn) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(url) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(route_name) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        return $query->get();
    }

    public function render()
    {
        // Get all items with their relationships
        $allItems = $this->getFilteredItems();

        // Organize items hierarchically (parents first, then their children)
        $items = $this->organizeNavigationItemsHierarchically($allItems);

        // Get parent items for the dropdown (excluding the current item being edited and its children to prevent circular references)
        $excludeIds = [$this->editingId ?? 0];
        if ($this->editingId) {
            // Also exclude all descendants of the current item
            $descendants = NavigationItem::where('parent_id', $this->editingId)->pluck('id')->toArray();
            $excludeIds = array_merge($excludeIds, $descendants);
            // Recursively get all descendants
            $allDescendants = [];
            while (! empty($descendants)) {
                $nextLevel = NavigationItem::whereIn('parent_id', $descendants)->pluck('id')->toArray();
                $allDescendants = array_merge($allDescendants, $nextLevel);
                $descendants = $nextLevel;
            }
            $excludeIds = array_merge($excludeIds, $allDescendants);
        }

        $parentOptions = NavigationItem::whereNotIn('id', $excludeIds)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        // Get all active categories
        $allCategories = Category::with('parent')
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('order')
            ->get();

        // Get categories that are already in navigation (by checking URL)
        $categoryUrls = $allItems->pluck('url')->filter(function ($url) {
            return str_starts_with($url, '/category/');
        })->map(function ($url) {
            return str_replace('/category/', '', $url);
        })->toArray();

        // Get available categories (not yet in navigation)
        $availableCategories = $allCategories->filter(function ($category) use ($categoryUrls) {
            return ! in_array($category->slug, $categoryUrls);
        });

        // Filter by search if provided
        if ($this->categorySearch) {
            $searchTerm = strtolower($this->categorySearch);
            $availableCategories = $availableCategories->filter(function ($category) use ($searchTerm) {
                return str_contains(strtolower($category->name_en), $searchTerm) ||
                    ($category->name_bn && str_contains(strtolower($category->name_bn), $searchTerm));
            });
        }

        // Organize available categories in parent-child hierarchy
        $availableCategories = $this->organizeCategoriesHierarchically($availableCategories);

        // Limit initial display
        $initialLimit = 10;
        $totalCount = $availableCategories->count();
        $displayCategories = $this->showAllCategories || $totalCount <= $initialLimit
            ? $availableCategories
            : $availableCategories->take($initialLimit);

        $navbarComponents = NavbarComponent::orderBy("order_{$this->activeZone}")->get();
        $navbarRegions = [
            'start' => $navbarComponents->where('zone_desktop', 'start')->values(),
            'middle' => $navbarComponents->where('zone_desktop', 'middle')->values(),
            'end' => $navbarComponents->where('zone_desktop', 'end')->values(),
        ];

        return view('livewire.admin.navigation.index', [
            'items' => $items,
            'parentOptions' => $parentOptions,
            'availableCategories' => $displayCategories,
            'totalCategoryCount' => $totalCount,
            'hasMoreCategories' => $totalCount > $initialLimit && ! $this->showAllCategories,
            'navbarComponents' => $navbarComponents,
            'navbarRegions' => $navbarRegions,
        ])->layout('components.layouts.app', [
            'title' => __('Navigation Settings'),
        ]);
    }

    /**
     * Organize navigation items in a hierarchical parent-child structure.
     */
    private function organizeNavigationItemsHierarchically($items): \Illuminate\Support\Collection
    {
        // Get parent items (those without parent_id)
        $parentItems = $items->filter(function ($item) {
            return $item->parent_id === null;
        })->sortBy('order');

        // Get child items
        $childItems = $items->filter(function ($item) {
            return $item->parent_id !== null;
        });

        // Track which children have been added
        $addedChildIds = [];
        $organized = collect();

        // Add parents and their children
        foreach ($parentItems as $parent) {
            $organized->push($parent);
            // Add children of this parent
            $children = $childItems->filter(function ($item) use ($parent) {
                return $item->parent_id === $parent->id;
            })->sortBy('order');
            foreach ($children as $child) {
                $organized->push($child);
                $addedChildIds[] = $child->id;
            }
        }

        // Add any orphaned children (whose parent is not in the list)
        $parentIds = $items->pluck('id')->toArray();
        $orphanedChildren = $childItems
            ->filter(function ($item) use ($parentIds, $addedChildIds) {
                return ! in_array($item->parent_id, $parentIds) && ! in_array($item->id, $addedChildIds);
            })
            ->sortBy('order');
        foreach ($orphanedChildren as $orphan) {
            $organized->push($orphan);
        }

        return $organized;
    }

    /**
     * Organize categories in a hierarchical parent-child structure.
     */
    private function organizeCategoriesHierarchically($categories): \Illuminate\Support\Collection
    {
        // Get parent categories (those without parent_id)
        $parentCategories = $categories->filter(function ($category) {
            return $category->parent_id === null;
        })->sortBy('order');

        // Get child categories
        $childCategories = $categories->filter(function ($category) {
            return $category->parent_id !== null;
        });

        // Track which children have been added
        $addedChildIds = [];
        $organized = collect();

        // Add parents and their children
        foreach ($parentCategories as $parent) {
            $organized->push($parent);
            // Add children of this parent
            $children = $childCategories->filter(function ($category) use ($parent) {
                return $category->parent_id === $parent->id;
            })->sortBy('order');
            foreach ($children as $child) {
                $organized->push($child);
                $addedChildIds[] = $child->id;
            }
        }

        // Add any orphaned children (whose parent is not in available categories)
        $parentIds = $categories->pluck('id')->toArray();
        $orphanedChildren = $childCategories
            ->filter(function ($category) use ($parentIds, $addedChildIds) {
                return ! in_array($category->parent_id, $parentIds) && ! in_array($category->id, $addedChildIds);
            })
            ->sortBy('order');
        foreach ($orphanedChildren as $orphan) {
            $organized->push($orphan);
        }

        return $organized;
    }
}
