<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterPaymentStatus = '';

    public $filterDateFrom = '';

    public $filterDateTo = '';

    public $perPage = 15;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $showOrderDetailsModal = false;

    public $selectedOrder = null;

    public $showCreateOrderModal = false;

    public $selectedItems = [];

    public $selectAll = false;

    public $customer_name = '';

    public $customer_email = '';

    public $customer_phone = '';

    public $shipping_address = '';

    public $shipping_city = '';

    public $shipping_postal_code = '';

    public $payment_method = 'cash';

    public $status = 'pending';

    public $notes = '';

    public $advance_payment = 0;

    public $transaction_id = '';

    public $advance_payment_method = '';

    public $orderItems = [];

    public $selectedProductId = null;

    public $selectedProductQuantity = 1;

    public $editingAdvancePayment = false;

    public $editAdvancePaymentOrderId = null;

    public $editAdvancePaymentAmount = 0;

    public $editTransactionId = '';

    public $editAdvancePaymentMethod = '';

    protected $queryString = ['search', 'filterStatus', 'filterPaymentStatus', 'filterDateFrom', 'filterDateTo', 'perPage', 'sortField', 'sortDirection'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPaymentStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo(): void
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
            $this->selectedItems = $this->getOrdersQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function bulkUpdateStatus($status): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one order.'));

            return;
        }

        Order::whereIn('id', $this->selectedItems)->update(['status' => $status]);
        session()->flash('message', __(':count order(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    protected function getOrdersQuery()
    {
        return Order::query()
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_name', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_email', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_phone', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterPaymentStatus, function ($query) {
                if ($this->filterPaymentStatus === 'fully_paid') {
                    $query->whereColumn('advance_payment', '>=', 'total');
                } elseif ($this->filterPaymentStatus === 'partially_paid') {
                    $query->whereColumn('advance_payment', '>', 0)
                        ->whereColumn('advance_payment', '<', 'total');
                } elseif ($this->filterPaymentStatus === 'unpaid') {
                    $query->where(function ($q) {
                        $q->whereColumn('advance_payment', '<=', 0)
                            ->orWhereNull('advance_payment');
                    });
                }
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            });
    }

    public function updateOrderStatus($orderId, $status): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        session()->flash('message', __('Order status updated successfully.'));
    }

    public function viewOrder($orderId): void
    {
        $this->selectedOrder = Order::with('orderItems')->findOrFail($orderId);
        $this->showOrderDetailsModal = true;
    }

    public function openCreateOrderModal(): void
    {
        $this->reset(['customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'shipping_city', 'shipping_postal_code', 'payment_method', 'status', 'notes', 'advance_payment', 'transaction_id', 'advance_payment_method', 'orderItems', 'selectedProductId', 'selectedProductQuantity']);
        $this->showCreateOrderModal = true;
    }

    public function closeCreateOrderModal(): void
    {
        $this->showCreateOrderModal = false;
    }

    public function addOrderItem(): void
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'selectedProductQuantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('productAttributes')->findOrFail($this->selectedProductId);
        $syncedPrice = $product->getSyncedPrice();

        // Check if product already in order items
        $existingIndex = collect($this->orderItems)->search(function ($item) use ($product) {
            return $item['product_id'] === $product->id;
        });

        if ($existingIndex !== false) {
            // Update quantity if product already exists
            $this->orderItems[$existingIndex]['quantity'] += $this->selectedProductQuantity;
            $this->orderItems[$existingIndex]['subtotal'] = $this->orderItems[$existingIndex]['quantity'] * $this->orderItems[$existingIndex]['price'];
        } else {
            // Add new item
            $this->orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name_en,
                'price' => $syncedPrice,
                'quantity' => $this->selectedProductQuantity,
                'subtotal' => $syncedPrice * $this->selectedProductQuantity,
            ];
        }

        $this->selectedProductId = null;
        $this->selectedProductQuantity = 1;
    }

    public function removeOrderItem($index): void
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
    }

    public function updatedOrderItems(): void
    {
        // Recalculate all subtotals when order items change
        foreach ($this->orderItems as $index => $item) {
            if (isset($item['quantity']) && $item['quantity'] > 0) {
                $this->orderItems[$index]['subtotal'] = $item['price'] * $item['quantity'];
            }
        }
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->orderItems)->sum('subtotal');
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal;
    }

    public function createOrder(): void
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:cash,bank_transfer,mobile_banking',
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'orderItems' => 'required|array|min:1',
            'advance_payment' => 'nullable|numeric|min:0|max:'.$this->total,
            'transaction_id' => 'nullable|string|max:255',
            'advance_payment_method' => 'nullable|string|in:cash,bank_transfer,mobile_banking,bkash,nagad,rocket',
        ]);

        $order = Order::create([
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'shipping_address' => $this->shipping_address,
            'shipping_city' => $this->shipping_city,
            'shipping_postal_code' => $this->shipping_postal_code,
            'subtotal' => $this->subtotal,
            'discount' => 0,
            'shipping_cost' => 0, // Manual orders default to 0 shipping, can be updated later if needed
            'total' => $this->total,
            'advance_payment' => $this->advance_payment ?? 0,
            'transaction_id' => $this->transaction_id ?: null,
            'advance_payment_method' => $this->advance_payment_method ?: null,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        foreach ($this->orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        session()->flash('message', __('Order created successfully.'));
        $this->closeCreateOrderModal();
    }

    public function openEditAdvancePayment($orderId): void
    {
        $order = Order::findOrFail($orderId);
        $this->editAdvancePaymentOrderId = $orderId;
        $this->editAdvancePaymentAmount = $order->advance_payment;
        $this->editTransactionId = $order->transaction_id ?? '';
        $this->editAdvancePaymentMethod = $order->advance_payment_method ?? '';
        $this->editingAdvancePayment = true;
    }

    public function closeEditAdvancePayment(): void
    {
        $this->editingAdvancePayment = false;
        $this->editAdvancePaymentOrderId = null;
        $this->editAdvancePaymentAmount = 0;
        $this->editTransactionId = '';
        $this->editAdvancePaymentMethod = '';
    }

    public function updateAdvancePayment(): void
    {
        $order = Order::findOrFail($this->editAdvancePaymentOrderId);

        $this->validate([
            'editAdvancePaymentAmount' => 'required|numeric|min:0|max:'.$order->total,
            'editTransactionId' => 'nullable|string|max:255',
            'editAdvancePaymentMethod' => 'nullable|string|in:cash,bank_transfer,mobile_banking,bkash,nagad,rocket',
        ]);

        $order->update([
            'advance_payment' => $this->editAdvancePaymentAmount,
            'transaction_id' => $this->editTransactionId ?: null,
            'advance_payment_method' => $this->editAdvancePaymentMethod ?: null,
        ]);

        session()->flash('message', __('Advance payment updated successfully.'));
        $this->closeEditAdvancePayment();

        // Refresh the selected order if viewing details
        if ($this->showOrderDetailsModal && $this->selectedOrder && $this->selectedOrder->id === $order->id) {
            $this->selectedOrder = Order::with('orderItems')->findOrFail($order->id);
        }
    }

    public function render()
    {
        $orders = $this->getOrdersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Order::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'pending' => Order::where('status', 'pending')->count(),
            'today' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
        ];

        return view('livewire.admin.orders.index', [
            'orders' => $orders,
            'products' => Product::with('productAttributes')->where('is_active', true)->orderBy('name_en')->get(),
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Orders'),
        ]);
    }
}
