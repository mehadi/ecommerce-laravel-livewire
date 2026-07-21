<?php

namespace App\Livewire\Admin\PurchaseOrders;

use App\Enums\StockMovementType;
use App\Models\ProductBatch;
use App\Models\PurchaseOrder;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $perPage = 15;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Receive modal
    public $showReceiveModal = false;

    public $receivingOrderId = null;

    /** @var array<int, int> purchase_order_item id => quantity to receive now */
    public array $receiveQuantities = [];

    /** @var array<int, string> purchase_order_item id => batch number (only for tracks_batches products) */
    public array $receiveBatchNumbers = [];

    /** @var array<int, string|null> purchase_order_item id => expiry date (only for tracks_batches products) */
    public array $receiveExpiryDates = [];

    protected $queryString = ['search', 'filterStatus', 'perPage', 'sortField', 'sortDirection'];

    public function updatingSearch(): void
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

    public function openReceiveModal($orderId): void
    {
        $order = PurchaseOrder::with('items.product')->findOrFail($orderId);

        $this->receivingOrderId = $orderId;
        $this->receiveQuantities = $order->items->mapWithKeys(fn ($item) => [$item->id => $item->remainingQuantity()])->all();
        $this->receiveBatchNumbers = $order->items->mapWithKeys(fn ($item) => [$item->id => ''])->all();
        $this->receiveExpiryDates = $order->items->mapWithKeys(fn ($item) => [$item->id => null])->all();
        $this->showReceiveModal = true;
    }

    public function closeReceiveModal(): void
    {
        $this->showReceiveModal = false;
        $this->receivingOrderId = null;
        $this->receiveQuantities = [];
        $this->receiveBatchNumbers = [];
        $this->receiveExpiryDates = [];
    }

    public function receivePurchaseOrder(): void
    {
        $order = PurchaseOrder::with('items.product')->findOrFail($this->receivingOrderId);

        if (! $order->canBeReceived()) {
            session()->flash('error', __('This purchase order has already been fully received or cancelled.'));
            $this->closeReceiveModal();

            return;
        }

        $this->validate([
            'receiveQuantities.*' => 'required|integer|min:0',
            'receiveBatchNumbers.*' => 'nullable|string|max:255',
            'receiveExpiryDates.*' => 'nullable|date',
        ]);

        foreach ($order->items as $item) {
            $additionalQty = (int) ($this->receiveQuantities[$item->id] ?? 0);
            $additionalQty = min($additionalQty, $item->remainingQuantity());

            if ($additionalQty <= 0) {
                continue;
            }

            if ($item->product?->tracks_batches && ! $item->product_attribute_id) {
                $batchNumber = trim((string) ($this->receiveBatchNumbers[$item->id] ?? ''));

                if ($batchNumber === '') {
                    $this->addError("receiveBatchNumbers.{$item->id}", __('A batch number is required for this product.'));

                    continue;
                }

                StockMovementContext::run([
                    'type' => StockMovementType::Receiving,
                    'reason' => "Purchase order #{$order->order_number} received",
                    'changed_by' => auth()->id(),
                ], function () use ($order, $item, $batchNumber, $additionalQty) {
                    ProductBatch::create([
                        'warehouse_id' => $order->warehouse_id,
                        'product_id' => $item->product_id,
                        'batch_number' => $batchNumber,
                        'quantity' => $additionalQty,
                        'expires_at' => $this->receiveExpiryDates[$item->id] ?: null,
                        'received_at' => now(),
                    ]);
                });
            } else {
                $warehouseStock = WarehouseStock::findOrCreateFor($order->warehouse_id, $item->product_id, $item->product_attribute_id);

                StockMovementContext::run([
                    'type' => StockMovementType::Receiving,
                    'reason' => "Purchase order #{$order->order_number} received",
                    'changed_by' => auth()->id(),
                ], function () use ($warehouseStock, $additionalQty) {
                    $warehouseStock->update(['stock' => $warehouseStock->stock + $additionalQty]);
                });
            }

            $item->update(['quantity_received' => $item->quantity_received + $additionalQty]);
        }

        $order->refresh();
        $fullyReceived = $order->items->every(fn ($item) => $item->quantity_received >= $item->quantity_ordered);
        $anyReceived = $order->items->sum('quantity_received') > 0;

        $order->update([
            'status' => $fullyReceived ? 'received' : ($anyReceived ? 'partially_received' : $order->status),
            'received_at' => $fullyReceived ? now() : $order->received_at,
        ]);

        session()->flash('message', __('Purchase order receipt recorded successfully.'));
        $this->closeReceiveModal();
    }

    public function cancelPurchaseOrder($orderId): void
    {
        $order = PurchaseOrder::findOrFail($orderId);

        if (! $order->canBeCancelled()) {
            session()->flash('error', __('This purchase order can no longer be cancelled.'));

            return;
        }

        $order->update(['status' => 'cancelled']);
        session()->flash('message', __('Purchase order cancelled.'));
    }

    protected function getOrdersQuery()
    {
        return PurchaseOrder::query()
            ->with(['supplier', 'warehouse', 'items'])
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('supplier', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('status', $this->filterStatus);
            });
    }

    public function render()
    {
        $orders = $this->getOrdersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => PurchaseOrder::count(),
            'open' => PurchaseOrder::whereIn('status', ['draft', 'ordered', 'partially_received'])->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
            'cancelled' => PurchaseOrder::where('status', 'cancelled')->count(),
        ];

        return view('livewire.admin.purchase-orders.index', [
            'orders' => $orders,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Purchase Orders'),
        ]);
    }
}
