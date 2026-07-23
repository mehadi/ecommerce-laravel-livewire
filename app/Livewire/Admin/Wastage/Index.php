<?php

namespace App\Livewire\Admin\Wastage;

use App\Enums\StockMovementType;
use App\Enums\WastageReason;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WastageLog;
use App\Notifications\WastageReportedForApproval;
use App\Support\StockMovementContext;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterReason = '';

    public $filterWarehouse = '';

    public $perPage = 15;

    protected $queryString = ['search', 'filterStatus', 'filterReason', 'filterWarehouse', 'perPage'];

    // Report modal
    public $showReportModal = false;

    public $productSearch = '';

    public $reportProductId = null;

    public $reportProductAttributeId = null;

    public $reportProductBatchId = null;

    public $reportWarehouseId = null;

    public $reportQuantity = 1;

    public $reportReason = 'damage';

    public $reportNotes = '';

    public $photo = null;

    // Review modal
    public $showReviewModal = false;

    public ?WastageLog $reviewingLog = null;

    public $reviewNotes = '';

    public function mount(): void
    {
        Gate::authorize('view wastage');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterReason(): void
    {
        $this->resetPage();
    }

    public function updatingFilterWarehouse(): void
    {
        $this->resetPage();
    }

    public function openReportModal(): void
    {
        Gate::authorize('create wastage');

        $this->reset(['productSearch', 'reportProductId', 'reportProductAttributeId', 'reportProductBatchId', 'reportQuantity', 'reportNotes', 'photo']);
        $this->reportReason = 'damage';
        $this->reportWarehouseId = Warehouse::default()->id;
        $this->showReportModal = true;
    }

    public function closeReportModal(): void
    {
        $this->showReportModal = false;
    }

    public function updatedReportProductId(): void
    {
        $this->reportProductAttributeId = null;
        $this->reportProductBatchId = null;
    }

    public function updatedReportWarehouseId(): void
    {
        $this->reportProductBatchId = null;
    }

    public function selectReportProduct(int $productId): void
    {
        $this->reportProductId = $productId;
        $this->reportProductAttributeId = null;
        $this->reportProductBatchId = null;
        $this->productSearch = '';
    }

    public function clearReportProduct(): void
    {
        $this->reportProductId = null;
        $this->reportProductAttributeId = null;
        $this->reportProductBatchId = null;
    }

    #[Computed]
    public function reportableProducts()
    {
        if ($this->productSearch === '') {
            return collect();
        }

        return Product::query()
            ->where(function ($query) {
                $query->where('name_en', 'like', '%'.$this->productSearch.'%')
                    ->orWhere('name_bn', 'like', '%'.$this->productSearch.'%')
                    ->orWhere('sku', 'like', '%'.$this->productSearch.'%');
            })
            ->orderBy('name_en')
            ->limit(25)
            ->get();
    }

    #[Computed]
    public function reportBatches()
    {
        $product = $this->reportProductId ? Product::find($this->reportProductId) : null;

        if (! $product || ! $product->tracks_batches || ! $this->reportWarehouseId) {
            return collect();
        }

        return ProductBatch::where('warehouse_id', $this->reportWarehouseId)
            ->where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->get();
    }

    public function saveReport(): void
    {
        Gate::authorize('create wastage');

        $tenantId = Tenancy::id();

        $product = Product::find($this->reportProductId);

        $this->validate([
            'reportProductId' => ['required', Rule::exists('products', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'reportProductAttributeId' => ['nullable', Rule::exists('product_attributes', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)->where('product_id', $this->reportProductId))],
            'reportProductBatchId' => ['nullable', Rule::exists('product_batches', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)->where('product_id', $this->reportProductId)->where('warehouse_id', $this->reportWarehouseId))],
            'reportWarehouseId' => ['required', Rule::exists('warehouses', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'reportQuantity' => 'required|integer|min:1',
            'reportReason' => ['required', Rule::enum(WastageReason::class)],
            'reportNotes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (! $product) {
            $this->addError('reportProductId', __('Select a product.'));

            return;
        }

        $available = $this->availableQuantity($product);

        if ($this->reportQuantity > $available) {
            $this->addError('reportQuantity', __('Only :available unit(s) available to report as wastage.', ['available' => $available]));

            return;
        }

        $photoPath = $this->photo ? $this->photo->store(Tenancy::storagePath('wastage'), 'public') : null;

        $log = WastageLog::create([
            'product_id' => $product->id,
            'product_attribute_id' => $this->reportProductAttributeId ?: null,
            'product_batch_id' => $this->reportProductBatchId ?: null,
            'warehouse_id' => $this->reportWarehouseId,
            'quantity' => $this->reportQuantity,
            'reason' => $this->reportReason,
            'notes' => $this->reportNotes,
            'photo_path' => $photoPath,
            'status' => 'pending',
            'reported_by' => auth()->id(),
        ]);

        $this->notifyApprovers($log);

        session()->flash('message', __('Wastage reported and sent for approval.'));
        $this->showReportModal = false;
    }

    private function availableQuantity(Product $product): int
    {
        if ($this->reportProductBatchId) {
            return (int) (ProductBatch::find($this->reportProductBatchId)?->quantity ?? 0);
        }

        return (int) (WarehouseStock::where('warehouse_id', $this->reportWarehouseId)
            ->where('product_id', $product->id)
            ->where('product_attribute_id', $this->reportProductAttributeId ?: null)
            ->value('stock') ?? 0);
    }

    private function notifyApprovers(WastageLog $log): void
    {
        $tenant = Tenancy::current();

        if (! $tenant) {
            return;
        }

        $recipients = User::whereHas('roles', fn ($query) => $query->whereIn('name', ['super admin', 'admin', 'manager']))->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new WastageReportedForApproval($log, $tenant));
        }
    }

    public function openReviewModal(int $wastageLogId): void
    {
        Gate::authorize('approve wastage');

        $this->reviewingLog = WastageLog::with(['product', 'productAttribute', 'productBatch', 'warehouse', 'reportedBy'])->findOrFail($wastageLogId);
        $this->reviewNotes = '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal(): void
    {
        $this->showReviewModal = false;
        $this->reviewingLog = null;
    }

    public function approve(): void
    {
        Gate::authorize('approve wastage');

        if (! $this->reviewingLog || ! $this->reviewingLog->canBeReviewed()) {
            session()->flash('error', __('This wastage report has already been reviewed.'));
            $this->closeReviewModal();

            return;
        }

        $log = $this->reviewingLog;

        $available = $log->product_batch_id
            ? (int) (ProductBatch::find($log->product_batch_id)?->quantity ?? 0)
            : (int) (WarehouseStock::where('warehouse_id', $log->warehouse_id)
                ->where('product_id', $log->product_id)
                ->where('product_attribute_id', $log->product_attribute_id)
                ->value('stock') ?? 0);

        if ($log->quantity > $available) {
            session()->flash('error', __('Stock has changed since this was reported — only :available unit(s) now available. Reject or ask for a corrected report.', ['available' => $available]));

            return;
        }

        StockMovementContext::run([
            'type' => StockMovementType::Wastage,
            'reason' => $log->reason->label().($log->notes ? " — {$log->notes}" : ''),
            'changed_by' => auth()->id(),
        ], function () use ($log) {
            if ($log->product_batch_id) {
                $batch = ProductBatch::findOrFail($log->product_batch_id);
                $batch->update(['quantity' => max(0, $batch->quantity - $log->quantity)]);
            } else {
                $warehouseStock = WarehouseStock::findOrCreateFor($log->warehouse_id, $log->product_id, $log->product_attribute_id);
                $warehouseStock->update(['stock' => max(0, $warehouseStock->stock - $log->quantity)]);
            }
        });

        $log->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);

        session()->flash('message', __('Wastage approved and stock deducted.'));
        $this->closeReviewModal();
    }

    public function reject(): void
    {
        Gate::authorize('approve wastage');

        if (! $this->reviewingLog || ! $this->reviewingLog->canBeReviewed()) {
            session()->flash('error', __('This wastage report has already been reviewed.'));
            $this->closeReviewModal();

            return;
        }

        $this->reviewingLog->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);

        session()->flash('message', __('Wastage report rejected.'));
        $this->closeReviewModal();
    }

    protected function computeStats(): array
    {
        return [
            'pending' => WastageLog::where('status', 'pending')->count(),
            'approved_qty_month' => (int) WastageLog::where('status', 'approved')
                ->whereMonth('reviewed_at', now()->month)
                ->whereYear('reviewed_at', now()->year)
                ->sum('quantity'),
            'rejected_month' => WastageLog::where('status', 'rejected')
                ->whereMonth('reviewed_at', now()->month)
                ->whereYear('reviewed_at', now()->year)
                ->count(),
            'value_month' => (float) WastageLog::query()
                ->join('products', 'products.id', '=', 'wastage_logs.product_id')
                ->where('wastage_logs.status', 'approved')
                ->whereMonth('wastage_logs.reviewed_at', now()->month)
                ->whereYear('wastage_logs.reviewed_at', now()->year)
                ->selectRaw('COALESCE(SUM(COALESCE(products.buying_price, 0) * wastage_logs.quantity), 0) as value')
                ->value('value'),
        ];
    }

    public function render()
    {
        $logs = WastageLog::query()
            ->with(['product', 'productAttribute', 'warehouse', 'reportedBy'])
            ->when($this->search !== '', function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name_en', 'like', '%'.$this->search.'%')
                        ->orWhere('name_bn', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatus !== '', fn ($query) => $query->where('status', $this->filterStatus))
            ->when($this->filterReason !== '', fn ($query) => $query->where('reason', $this->filterReason))
            ->when($this->filterWarehouse !== '', fn ($query) => $query->where('warehouse_id', $this->filterWarehouse))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.wastage.index', [
            'logs' => $logs,
            'stats' => $this->computeStats(),
            'activeWarehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'reportableProducts' => $this->reportableProducts,
            'reportBatches' => $this->reportBatches,
            'reportProduct' => $this->reportProductId ? Product::with('productAttributes')->find($this->reportProductId) : null,
        ])->layout('components.layouts.app', [
            'title' => __('Wastage'),
        ]);
    }
}
