<?php

namespace App\Livewire\Admin\PurchaseOrders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Component;

class Create extends Component
{
    public $supplier_id = null;

    public $warehouse_id = null;

    public $notes = '';

    public $expected_at = null;

    /** @var array<int, array{product_id: ?int, product_attribute_id: ?int, quantity_ordered: int, unit_cost: ?float}> */
    public array $items = [];

    public function mount(): void
    {
        $prefillSupplierId = session('prefill_po_supplier_id');
        $prefillItems = session('prefill_po_items');

        if ($prefillSupplierId && $prefillItems) {
            $this->supplier_id = $prefillSupplierId;
            $this->items = collect($prefillItems)->map(fn ($item) => [
                'product_id' => $item['product_id'],
                'product_attribute_id' => null,
                'quantity_ordered' => $item['quantity_ordered'],
                'unit_cost' => null,
            ])->all();
        } else {
            $this->items = [
                ['product_id' => null, 'product_attribute_id' => null, 'quantity_ordered' => 1, 'unit_cost' => null],
            ];
        }

        $this->warehouse_id = Warehouse::default()->id;
    }

    protected function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string|max:1000',
            'expected_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_attribute_id' => 'nullable|exists:product_attributes,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'product_attribute_id' => null, 'quantity_ordered' => 1, 'unit_cost' => null];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function attributesForProduct(?int $productId)
    {
        if (! $productId) {
            return collect();
        }

        return Product::find($productId)?->productAttributes ?? collect();
    }

    public function save(): void
    {
        $this->validate();

        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $this->supplier_id,
            'warehouse_id' => $this->warehouse_id,
            'status' => 'ordered',
            'notes' => $this->notes,
            'ordered_at' => now(),
            'expected_at' => $this->expected_at,
        ]);

        foreach ($this->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $item['product_id'],
                'product_attribute_id' => $item['product_attribute_id'] ?: null,
                'quantity_ordered' => (int) $item['quantity_ordered'],
                'unit_cost' => $item['unit_cost'] !== null && $item['unit_cost'] !== '' ? (float) $item['unit_cost'] : null,
            ]);
        }

        session()->flash('message', __('Purchase order created successfully.'));
        $this->redirect(route('admin.purchase-orders.index'));
    }

    public function render()
    {
        return view('livewire.admin.purchase-orders.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name_en')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('New Purchase Order'),
        ]);
    }
}
