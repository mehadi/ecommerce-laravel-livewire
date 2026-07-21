<?php

namespace App\Livewire\Admin\StockTransfers;

use App\Enums\StockMovementType;
use App\Models\StockTransfer;
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

    public $receivingTransferId = null;

    /** @var array<int, int> stock_transfer_item id => quantity received */
    public array $receiveQuantities = [];

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

    public function openReceiveModal($transferId): void
    {
        $transfer = StockTransfer::with('items')->findOrFail($transferId);

        $this->receivingTransferId = $transferId;
        $this->receiveQuantities = $transfer->items->mapWithKeys(fn ($item) => [$item->id => $item->quantity])->all();
        $this->showReceiveModal = true;
    }

    public function closeReceiveModal(): void
    {
        $this->showReceiveModal = false;
        $this->receivingTransferId = null;
        $this->receiveQuantities = [];
    }

    public function receiveTransfer(): void
    {
        $this->validate([
            'receiveQuantities.*' => 'required|integer|min:0',
        ]);

        $transfer = StockTransfer::with('items')->findOrFail($this->receivingTransferId);

        if (! $transfer->canBeReceived()) {
            session()->flash('error', __('This transfer has already been received or cancelled.'));
            $this->closeReceiveModal();

            return;
        }

        foreach ($transfer->items as $item) {
            $quantityReceived = (int) ($this->receiveQuantities[$item->id] ?? $item->quantity);

            $sourceStock = WarehouseStock::findOrCreateFor($transfer->from_warehouse_id, $item->product_id, $item->product_attribute_id);
            $destinationStock = WarehouseStock::findOrCreateFor($transfer->to_warehouse_id, $item->product_id, $item->product_attribute_id);

            StockMovementContext::run([
                'type' => StockMovementType::TransferOut,
                'reason' => "Transfer #{$transfer->id} to {$transfer->toWarehouse->name}",
                'changed_by' => auth()->id(),
            ], function () use ($sourceStock, $quantityReceived) {
                $sourceStock->update(['stock' => max(0, $sourceStock->stock - $quantityReceived)]);
            });

            StockMovementContext::run([
                'type' => StockMovementType::TransferIn,
                'reason' => "Transfer #{$transfer->id} from {$transfer->fromWarehouse->name}",
                'changed_by' => auth()->id(),
            ], function () use ($destinationStock, $quantityReceived) {
                $destinationStock->update(['stock' => $destinationStock->stock + $quantityReceived]);
            });

            $item->update(['quantity_received' => $quantityReceived]);
        }

        $transfer->update(['status' => 'received', 'received_at' => now()]);

        session()->flash('message', __('Stock transfer received successfully.'));
        $this->closeReceiveModal();
    }

    public function cancelTransfer($transferId): void
    {
        $transfer = StockTransfer::findOrFail($transferId);

        if (! $transfer->canBeCancelled()) {
            session()->flash('error', __('This transfer can no longer be cancelled.'));

            return;
        }

        $transfer->update(['status' => 'cancelled']);
        session()->flash('message', __('Stock transfer cancelled.'));
    }

    protected function getTransfersQuery()
    {
        return StockTransfer::query()
            ->with(['fromWarehouse', 'toWarehouse', 'requestedBy', 'items'])
            ->when($this->search, function ($query) {
                $query->whereHas('fromWarehouse', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                    ->orWhereHas('toWarehouse', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('status', $this->filterStatus);
            });
    }

    public function render()
    {
        $transfers = $this->getTransfersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => StockTransfer::count(),
            'pending' => StockTransfer::whereIn('status', ['pending', 'in_transit'])->count(),
            'received' => StockTransfer::where('status', 'received')->count(),
            'cancelled' => StockTransfer::where('status', 'cancelled')->count(),
        ];

        return view('livewire.admin.stock-transfers.index', [
            'transfers' => $transfers,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Stock Transfers'),
        ]);
    }
}
