<?php

namespace App\Livewire\Admin\Inventory;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterCategory = '';

    public $filterStock = '';

    public $filterWarehouse = '';

    public $perPage = 15;

    public $sortField = 'name_en';

    public $sortDirection = 'asc';

    public $expandedProductId = null;

    protected $queryString = ['search', 'filterCategory', 'filterStock', 'filterWarehouse', 'perPage', 'sortField', 'sortDirection'];

    // Adjust stock modal
    public $showAdjustModal = false;

    public ?Product $adjustingProduct = null;

    public string $adjustMode = 'set';

    public $adjustWarehouseId = null;

    public $adjustQuantity = 0;

    public $adjustReason = '';

    /** @var array<int, int> product_attribute_id => new quantity (meaning depends on adjustMode) */
    public array $variantQuantities = [];

    /** @var array<int, array{id: ?int, batch_number: string, quantity: int, expires_at: ?string}> */
    public array $batchRows = [];

    // History modal
    public $showHistoryModal = false;

    public $historyProductId = null;

    // Default low stock threshold (tenant setting)
    public $showThresholdModal = false;

    public $lowStockThresholdSetting = '10';

    public function mount(): void
    {
        Gate::authorize('view inventory');

        $this->lowStockThresholdSetting = Setting::get('low_stock_threshold', '10');
    }

    public function updatingSearch(): void
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

    public function updatingFilterWarehouse(): void
    {
        $this->resetPage();
    }

    public function toggleExpand($productId): void
    {
        $this->expandedProductId = $this->expandedProductId === $productId ? null : $productId;
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

    public function openThresholdModal(): void
    {
        Gate::authorize('manage inventory settings');

        $this->lowStockThresholdSetting = Setting::get('low_stock_threshold', '10');
        $this->showThresholdModal = true;
    }

    public function closeThresholdModal(): void
    {
        $this->showThresholdModal = false;
    }

    public function saveThreshold(): void
    {
        Gate::authorize('manage inventory settings');

        $this->validate([
            'lowStockThresholdSetting' => 'required|integer|min:1',
        ]);

        Setting::set('low_stock_threshold', (string) $this->lowStockThresholdSetting);

        session()->flash('message', __('Default low stock threshold updated.'));
        $this->showThresholdModal = false;
    }

    public function recomputeAbcClasses(): void
    {
        Gate::authorize('manage inventory settings');

        Artisan::call('inventory:recompute-abc');

        session()->flash('message', __('ABC classification recalculated.'));
    }

    public function openAdjustModal($productId): void
    {
        Gate::authorize('adjust stock');

        $product = Product::with('productAttributes')->findOrFail($productId);

        $this->adjustingProduct = $product;
        $this->adjustMode = 'set';
        $this->adjustReason = '';
        $this->adjustWarehouseId = Warehouse::default()->id;
        $this->refreshAdjustModalState();

        $this->showAdjustModal = true;
    }

    public function closeAdjustModal(): void
    {
        $this->showAdjustModal = false;
        $this->adjustingProduct = null;
        $this->variantQuantities = [];
        $this->batchRows = [];
    }

    public function updatedAdjustWarehouseId(): void
    {
        $this->refreshAdjustModalState();
    }

    public function updatedAdjustMode(): void
    {
        $this->refreshAdjustModalState();
    }

    private function refreshAdjustModalState(): void
    {
        if ($this->adjustingProduct?->tracks_batches && ! $this->targetsReserved()) {
            $this->loadBatchRows();
        } else {
            $this->loadAdjustQuantities();
        }
    }

    /**
     * Seeds the quantity input(s) for the currently selected warehouse/mode:
     * 'set' seeds the current value (stock or reserved, whichever this mode
     * targets) so the field reads as "edit this to the new total"; every
     * other mode seeds 0 so the field reads as "how much to add/remove".
     */
    private function loadAdjustQuantities(): void
    {
        $product = $this->adjustingProduct;

        if (! $product || ! $this->adjustWarehouseId) {
            return;
        }

        $column = $this->targetsReserved() ? 'reserved' : 'stock';
        $seedCurrent = $this->adjustMode === 'set';

        if ($product->hasAttributes()) {
            $this->variantQuantities = $product->productAttributes->mapWithKeys(function ($variant) use ($column, $seedCurrent) {
                $current = (int) (WarehouseStock::where('warehouse_id', $this->adjustWarehouseId)
                    ->where('product_attribute_id', $variant->id)
                    ->value($column) ?? 0);

                return [$variant->id => $seedCurrent ? $current : 0];
            })->all();
        } else {
            $current = (int) (WarehouseStock::where('warehouse_id', $this->adjustWarehouseId)
                ->where('product_id', $product->id)
                ->whereNull('product_attribute_id')
                ->value($column) ?? 0);

            $this->adjustQuantity = $seedCurrent ? $current : 0;
        }
    }

    /**
     * For a tracks_batches product, editing "stock" means editing its
     * batches directly rather than one aggregate number — loads every
     * existing batch at the selected warehouse (soonest-expiring first,
     * the FEFO pick order) plus one blank row for adding a new batch.
     */
    private function loadBatchRows(): void
    {
        $product = $this->adjustingProduct;

        if (! $product || ! $this->adjustWarehouseId) {
            return;
        }

        $this->batchRows = ProductBatch::where('warehouse_id', $this->adjustWarehouseId)
            ->where('product_id', $product->id)
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->get()
            ->map(fn (ProductBatch $batch) => [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity' => $batch->quantity,
                'expires_at' => $batch->expires_at?->format('Y-m-d'),
            ])->values()->all();

        $this->batchRows[] = ['id' => null, 'batch_number' => '', 'quantity' => 0, 'expires_at' => null];
    }

    public function addBatchRow(): void
    {
        $this->batchRows[] = ['id' => null, 'batch_number' => '', 'quantity' => 0, 'expires_at' => null];
    }

    public function removeBatchRow(int $index): void
    {
        unset($this->batchRows[$index]);
        $this->batchRows = array_values($this->batchRows);
    }

    private function targetsReserved(): bool
    {
        return in_array($this->adjustMode, ['reserve', 'release'], true);
    }

    public function adjustStock(): void
    {
        Gate::authorize('adjust stock');

        $product = $this->adjustingProduct;

        if (! $product) {
            return;
        }

        if ($product->tracks_batches && ! $this->targetsReserved()) {
            $this->saveBatchRows($product);
            session()->flash('message', __('Stock adjusted successfully.'));
            $this->closeAdjustModal();

            return;
        }

        $this->validate([
            'adjustReason' => 'required|string|max:255',
            'adjustWarehouseId' => 'required|exists:warehouses,id',
            'adjustQuantity' => 'nullable|integer|min:0',
            'variantQuantities.*' => 'nullable|integer|min:0',
        ]);

        if ($product->hasAttributes()) {
            foreach ($product->productAttributes as $variant) {
                $input = (int) ($this->variantQuantities[$variant->id] ?? 0);
                $warehouseStock = WarehouseStock::findOrCreateFor($this->adjustWarehouseId, $product->id, $variant->id);
                $this->applyAdjustment($warehouseStock, $input);
            }
        } else {
            $input = (int) $this->adjustQuantity;
            $warehouseStock = WarehouseStock::findOrCreateFor($this->adjustWarehouseId, $product->id, null);
            $this->applyAdjustment($warehouseStock, $input);
        }

        session()->flash('message', __('Stock adjusted successfully.'));
        $this->closeAdjustModal();
    }

    private function applyAdjustment(WarehouseStock $warehouseStock, int $input): void
    {
        $column = $this->targetsReserved() ? 'reserved' : 'stock';
        $current = (int) $warehouseStock->{$column};

        $new = match ($this->adjustMode) {
            'add', 'reserve' => $current + $input,
            'remove', 'release' => max(0, $current - $input),
            default => $input, // 'set'
        };

        // A warehouse can never reserve more than it physically holds.
        if ($this->adjustMode === 'reserve') {
            $new = min($new, $warehouseStock->stock);
        }

        if ($new === $current) {
            return;
        }

        StockMovementContext::run([
            'type' => $this->targetsReserved() ? StockMovementType::Reservation : StockMovementType::Adjustment,
            'reason' => $this->adjustReason,
            'changed_by' => auth()->id(),
        ], function () use ($warehouseStock, $column, $new) {
            $warehouseStock->update([$column => $new]);
        });
    }

    /**
     * Applies edits from the batch-row UI: existing batches whose quantity
     * changed are updated in place; a blank trailing row with a batch
     * number and a positive quantity becomes a new batch. Both paths route
     * through ProductBatchObserver for logging + the warehouse/product
     * stock resync.
     */
    private function saveBatchRows(Product $product): void
    {
        $this->validate([
            'adjustReason' => 'required|string|max:255',
            'adjustWarehouseId' => 'required|exists:warehouses,id',
            'batchRows.*.batch_number' => 'nullable|string|max:255',
            'batchRows.*.quantity' => 'nullable|integer|min:0',
            'batchRows.*.expires_at' => 'nullable|date',
        ]);

        foreach ($this->batchRows as $row) {
            $quantity = (int) ($row['quantity'] ?? 0);

            if (! empty($row['id'])) {
                $batch = ProductBatch::find($row['id']);

                if ($batch && (int) $batch->quantity !== $quantity) {
                    StockMovementContext::run([
                        'type' => StockMovementType::Adjustment,
                        'reason' => $this->adjustReason,
                        'changed_by' => auth()->id(),
                    ], function () use ($batch, $quantity) {
                        $batch->update(['quantity' => $quantity]);
                    });
                }
            } elseif (! empty($row['batch_number']) && $quantity > 0) {
                StockMovementContext::run([
                    'type' => StockMovementType::Adjustment,
                    'reason' => $this->adjustReason,
                    'changed_by' => auth()->id(),
                ], function () use ($product, $row, $quantity) {
                    ProductBatch::create([
                        'warehouse_id' => $this->adjustWarehouseId,
                        'product_id' => $product->id,
                        'batch_number' => $row['batch_number'],
                        'quantity' => $quantity,
                        'expires_at' => $row['expires_at'] ?: null,
                        'received_at' => now(),
                    ]);
                });
            }
        }
    }

    public function openHistoryModal($productId): void
    {
        $this->historyProductId = $productId;
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->historyProductId = null;
    }

    protected function getProductsQuery()
    {
        return Product::query()
            ->with(['category', 'productAttributes.warehouseStocks', 'warehouseStocks'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name_en', 'like', '%'.$this->search.'%')
                        ->orWhere('name_bn', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterCategory !== '', function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterWarehouse !== '', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('warehouseStocks', fn ($wq) => $wq->where('warehouse_id', $this->filterWarehouse))
                        ->orWhereHas('productAttributes.warehouseStocks', fn ($wq) => $wq->where('warehouse_id', $this->filterWarehouse));
                });
            });
    }

    public function render()
    {
        $query = $this->getProductsQuery();

        if ($this->filterStock !== '') {
            $matchingIds = Product::with('productAttributes')
                ->get()
                ->filter(function (Product $product) {
                    return match ($this->filterStock) {
                        'in_stock' => $product->getSyncedStock() > 0,
                        'low_stock' => $product->isLowStock(),
                        'out_of_stock' => $product->getSyncedStock() <= 0,
                        default => true,
                    };
                })
                ->pluck('id');

            $query->whereIn('id', $matchingIds);
        }

        $products = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        $allProducts = Product::with('productAttributes.warehouseStocks', 'warehouseStocks')->get();

        $totalUnits = $allProducts->sum(fn (Product $product) => $product->getSyncedStock());
        $lowStockCount = $allProducts->filter(fn (Product $product) => $product->isLowStock())->count();
        $outOfStockCount = $allProducts->filter(fn (Product $product) => $product->getSyncedStock() <= 0)->count();
        $totalValue = $allProducts->sum(function (Product $product) {
            if ($product->hasAttributes()) {
                return $product->productAttributes->sum(fn ($variant) => (float) ($variant->buying_price ?? 0) * (int) $variant->stock);
            }

            return (float) ($product->buying_price ?? 0) * (int) $product->stock;
        });

        $stats = [
            'total_skus' => $allProducts->count(),
            'total_units' => $totalUnits,
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            'total_value' => $totalValue,
        ];

        $historyProduct = null;
        $historyMovements = collect();

        if ($this->historyProductId) {
            $historyProduct = Product::find($this->historyProductId);
            $historyMovements = $historyProduct
                ? $historyProduct->stockMovements()
                    ->with(['productAttribute', 'changedBy', 'warehouse'])
                    ->orderByDesc('changed_at')
                    ->limit(50)
                    ->get()
                : collect();
        }

        return view('livewire.admin.inventory.index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => Category::orderBy('name_en')->get(),
            'activeWarehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'hasAnyReservation' => WarehouseStock::where('reserved', '>', 0)->exists(),
            'hasAnyAbcClass' => Product::whereNotNull('abc_class')->exists(),
            'historyProduct' => $historyProduct,
            'historyMovements' => $historyMovements,
        ])->layout('components.layouts.app', [
            'title' => __('Inventory'),
        ]);
    }
}
