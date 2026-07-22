<?php

namespace App\Livewire\Admin\Inventory;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductBatch;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
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

    // Adjust Stock modal: current per-warehouse figure(s) being edited, shown
    // as a caption alongside the input(s) — distinct from $adjustQuantity /
    // $variantQuantities, which are seeded to 0 for every mode except 'set'.
    public int $adjustCurrentStock = 0;

    /** @var array<int, int> product_attribute_id => current per-warehouse quantity */
    public array $variantCurrentStocks = [];

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

        $tenantId = Tenancy::id();

        if (! $tenantId) {
            session()->flash('error', __('Unable to determine the current store.'));

            return;
        }

        // Scoped to this tenant only — recomputing every tenant on the
        // platform synchronously inline in a single request doesn't scale.
        Artisan::call('inventory:recompute-abc', ['--tenant' => $tenantId]);

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
        $this->variantCurrentStocks = [];
        $this->adjustCurrentStock = 0;
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
            $currentStocks = [];
            $quantities = [];

            foreach ($product->productAttributes as $variant) {
                $current = (int) (WarehouseStock::where('warehouse_id', $this->adjustWarehouseId)
                    ->where('product_attribute_id', $variant->id)
                    ->value($column) ?? 0);

                $currentStocks[$variant->id] = $current;
                $quantities[$variant->id] = $seedCurrent ? $current : 0;
            }

            $this->variantCurrentStocks = $currentStocks;
            $this->variantQuantities = $quantities;
        } else {
            $current = (int) (WarehouseStock::where('warehouse_id', $this->adjustWarehouseId)
                ->where('product_id', $product->id)
                ->whereNull('product_attribute_id')
                ->value($column) ?? 0);

            $this->adjustCurrentStock = $current;
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
            'adjustWarehouseId' => ['required', Rule::exists('warehouses', 'id')->where(fn ($query) => $query->where('tenant_id', Tenancy::id()))],
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
            'adjustWarehouseId' => ['required', Rule::exists('warehouses', 'id')->where(fn ($query) => $query->where('tenant_id', Tenancy::id()))],
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

    /**
     * `products.stock` is kept in sync with the sum of its warehouse/variant
     * stock by Product::syncPriceAndStock() (called from the WarehouseStock
     * and ProductAttribute observers on every write), so SQL filters/aggregates
     * against this column are equivalent to the per-product getSyncedStock()
     * used elsewhere, without loading every product into memory to compute it.
     */
    protected function getProductsQuery()
    {
        $defaultThreshold = (int) Setting::get('low_stock_threshold', '10');

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
            })
            ->when($this->filterStock !== '', function ($query) use ($defaultThreshold) {
                match ($this->filterStock) {
                    'in_stock' => $query->where('stock', '>', 0),
                    'out_of_stock' => $query->where('stock', '<=', 0),
                    'low_stock' => $query->where('stock', '>', 0)
                        ->whereRaw('stock <= COALESCE(low_stock_threshold, ?)', [$defaultThreshold]),
                    default => null,
                };
            });
    }

    /**
     * Tenant-wide stock stats via aggregate SQL rather than loading every
     * product (and every variant/warehouse-stock row) into memory each render.
     * Buying-price value is split base-product vs. variant because only
     * price/compare_at_price/stock are synced onto the parent product by
     * syncPriceAndStock() — buying_price is not, so a variant-tracked
     * product's own buying_price column can't be trusted for its value.
     */
    protected function computeStats(): array
    {
        $defaultThreshold = (int) Setting::get('low_stock_threshold', '10');

        $baseValue = (float) Product::doesntHave('productAttributes')
            ->selectRaw('COALESCE(SUM(COALESCE(buying_price, 0) * COALESCE(stock, 0)), 0) as value')
            ->value('value');

        $variantValue = (float) ProductAttribute::query()
            ->selectRaw('COALESCE(SUM(COALESCE(buying_price, 0) * COALESCE(stock, 0)), 0) as value')
            ->value('value');

        return [
            'total_skus' => Product::count(),
            'total_units' => (int) Product::sum('stock'),
            'low_stock' => Product::where('stock', '>', 0)
                ->whereRaw('stock <= COALESCE(low_stock_threshold, ?)', [$defaultThreshold])
                ->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
            'total_value' => $baseValue + $variantValue,
        ];
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = $this->computeStats();

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
