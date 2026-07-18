<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterCategory = '';

    public $filterStock = '';

    public $filterFeatured = '';

    public $perPage = 15;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterStatus', 'filterCategory', 'filterStock', 'filterFeatured', 'perPage', 'sortField', 'sortDirection'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStock(): void
    {
        $this->resetPage();
    }

    public function updatingFilterFeatured(): void
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
            $this->selectedItems = $this->getProductsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function toggleStatus($productId): void
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => ! $product->is_active]);
        $this->clearCache();
        session()->flash('message', __('Product status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$productId]);
    }

    public function toggleFeatured($productId): void
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_featured' => ! $product->is_featured]);
        $this->clearCache();
        session()->flash('message', __('Product featured status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$productId]);
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one product.'));

            return;
        }

        $products = Product::whereIn('id', $this->selectedItems)->get();
        $newStatus = $products->first()->is_active ? false : true;

        foreach ($products as $product) {
            $product->update(['is_active' => $newStatus]);
        }

        $this->clearCache();
        session()->flash('message', __(':count product(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkToggleFeatured(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one product.'));

            return;
        }

        $products = Product::whereIn('id', $this->selectedItems)->get();
        $newStatus = $products->first()->is_featured ? false : true;

        foreach ($products as $product) {
            $product->update(['is_featured' => $newStatus]);
        }

        $this->clearCache();
        session()->flash('message', __(':count product(s) featured status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one product.'));

            return;
        }

        $products = Product::whereIn('id', $this->selectedItems)->get();
        foreach ($products as $product) {
            if ($product->primary_image) {
                Storage::disk('public')->delete($product->primary_image);
            }
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        Product::whereIn('id', $this->selectedItems)->delete();
        $this->clearCache();
        session()->flash('message', __(':count product(s) deleted successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function deleteProduct($productId): void
    {
        $product = Product::findOrFail($productId);
        if ($product->primary_image) {
            Storage::disk('public')->delete($product->primary_image);
        }
        if ($product->gallery_images) {
            foreach ($product->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        $product->delete();
        $this->clearCache();
        session()->flash('message', __('Product deleted successfully.'));
    }

    public function delete(Product $product): void
    {
        $this->deleteProduct($product->id);
    }

    public function duplicateProduct($productId): void
    {
        $original = Product::findOrFail($productId);

        $newProduct = $original->replicate();
        $newProduct->name_en = ($original->name_en ? $original->name_en.' (Copy)' : null);
        $newProduct->name_bn = ($original->name_bn ? $original->name_bn.' (Copy)' : null);
        $newProduct->is_active = false;
        $newProduct->is_featured = false;
        $newProduct->sku = null; // SKU must be unique
        $newProduct->primary_image = null; // Don't duplicate image
        $newProduct->gallery_images = null; // Don't duplicate gallery images
        $maxOrder = Product::max('order');
        $newProduct->order = $maxOrder ? $maxOrder + 1 : 0;
        $newProduct->save();

        $this->clearCache();
        session()->flash('message', __('Product duplicated successfully.'));
    }

    protected function clearCache(): void
    {
        Cache::forget('products.featured');
    }

    protected function getProductsQuery()
    {
        return Product::query()
            ->with(['category', 'productAttributes'])
            ->when($this->search, function ($query) {
                $query->where('name_en', 'like', '%'.$this->search.'%')
                    ->orWhere('name_bn', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterCategory !== '', function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterStock !== '', function ($query) {
                if ($this->filterStock === 'in_stock') {
                    // For products with attributes, check product attributes stock
                    $query->where(function ($q) {
                        $q->whereHas('productAttributes', function ($subQ) {
                            $subQ->selectRaw('product_id, SUM(stock) as total_stock')
                                ->groupBy('product_id')
                                ->havingRaw('SUM(stock) > 0');
                        })->orWhere(function ($subQ) {
                            $subQ->whereDoesntHave('productAttributes')
                                ->where('stock', '>', 0);
                        });
                    });
                } elseif ($this->filterStock === 'low_stock') {
                    $query->where(function ($q) {
                        $q->whereHas('productAttributes', function ($subQ) {
                            $subQ->selectRaw('product_id, SUM(stock) as total_stock')
                                ->groupBy('product_id')
                                ->havingRaw('SUM(stock) > 0 AND SUM(stock) <= 10');
                        })->orWhere(function ($subQ) {
                            $subQ->whereDoesntHave('productAttributes')
                                ->where('stock', '>', 0)
                                ->where('stock', '<=', 10);
                        });
                    });
                } elseif ($this->filterStock === 'out_of_stock') {
                    $query->where(function ($q) {
                        $q->whereHas('productAttributes', function ($subQ) {
                            $subQ->selectRaw('product_id, SUM(stock) as total_stock')
                                ->groupBy('product_id')
                                ->havingRaw('SUM(stock) <= 0');
                        })->orWhere(function ($subQ) {
                            $subQ->whereDoesntHave('productAttributes')
                                ->where('stock', '<=', 0);
                        });
                    });
                }
            })
            ->when($this->filterFeatured !== '', function ($query) {
                if ($this->filterFeatured === 'featured') {
                    $query->where('is_featured', true);
                } elseif ($this->filterFeatured === 'not_featured') {
                    $query->where('is_featured', false);
                }
            });
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Calculate stats accounting for attributes
        $allProducts = Product::with('productAttributes')->get();

        $lowStockCount = 0;
        $outOfStockCount = 0;
        $totalValue = 0;

        foreach ($allProducts as $product) {
            $stock = $product->getSyncedStock();
            if ($stock <= 0) {
                $outOfStockCount++;
            } elseif ($stock <= 10) {
                $lowStockCount++;
            }
            $totalValue += $product->getSyncedPrice();
        }

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'featured' => Product::where('is_featured', true)->count(),
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            'total_value' => $totalValue,
        ];

        $categories = Category::orderBy('name_en')->get();

        return view('livewire.admin.products.index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => $categories,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Products'),
        ]);
    }
}
