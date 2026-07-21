<?php

namespace App\Livewire\Admin\Pos\Refunds;

use App\Models\Order;
use App\Models\PosShift;
use App\Services\PosRefundService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 15;

    public $showRefundModal = false;

    public $refundingOrderId = null;

    public $refundMethod = 'cash';

    public $refundReason = '';

    public $refundQuantities = [];

    public function mount(): void
    {
        Gate::authorize('process pos refunds');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openRefundModal($orderId): void
    {
        $order = Order::with('items')->findOrFail($orderId);

        $this->refundingOrderId = $orderId;
        $this->refundMethod = 'cash';
        $this->refundReason = '';
        $this->refundQuantities = $order->items->mapWithKeys(fn ($item) => [$item->id => 0])->all();
        $this->showRefundModal = true;
    }

    public function closeRefundModal(): void
    {
        $this->showRefundModal = false;
    }

    public function submitRefund(): void
    {
        Gate::authorize('process pos refunds');

        $order = Order::with('items')->findOrFail($this->refundingOrderId);
        $lines = collect($this->refundQuantities)->filter(fn ($qty) => (int) $qty > 0);

        if ($lines->isEmpty()) {
            session()->flash('error', __('Enter a quantity to refund for at least one item.'));

            return;
        }

        $currentShift = $this->refundMethod === 'cash' ? PosShift::where('status', 'open')->first() : null;

        DB::transaction(function () use ($order, $lines, $currentShift) {
            foreach ($lines as $itemId => $qty) {
                $item = $order->items->firstWhere('id', (int) $itemId);

                if (! $item) {
                    continue;
                }

                $refundable = $item->quantity - $item->refundedQuantity();
                $qty = min((int) $qty, $refundable);

                if ($qty <= 0) {
                    continue;
                }

                app(PosRefundService::class)->refund([
                    'order' => $order,
                    'order_item' => $item,
                    'quantity' => $qty,
                    'amount' => round($item->price * $qty, 2),
                    'method' => $this->refundMethod,
                    'reason' => $this->refundReason ?: null,
                    'current_shift' => $currentShift,
                ]);
            }
        });

        session()->flash('message', __('Refund processed successfully.'));
        $this->showRefundModal = false;
    }

    protected function getOrdersQuery()
    {
        return Order::query()
            ->where('channel', 'pos')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_name', 'like', '%'.$this->search.'%');
                });
            });
    }

    public function render()
    {
        $orders = $this->getOrdersQuery()
            ->withCount('refunds')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $refundingOrder = $this->refundingOrderId ? Order::with('items')->find($this->refundingOrderId) : null;

        return view('livewire.admin.pos.refunds.index', [
            'orders' => $orders,
            'refundingOrder' => $refundingOrder,
        ])->layout('components.layouts.app', [
            'title' => __('POS Refunds'),
        ]);
    }
}
