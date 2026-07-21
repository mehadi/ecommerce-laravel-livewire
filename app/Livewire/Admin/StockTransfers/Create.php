<?php

namespace App\Livewire\Admin\StockTransfers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use Livewire\Component;

class Create extends Component
{
    public $from_warehouse_id = null;

    public $to_warehouse_id = null;

    public $notes = '';

    /** @var array<int, array{product_id: ?int, product_attribute_id: ?int, quantity: int}> */
    public array $items = [];

    public function mount(): void
    {
        $this->items = [
            ['product_id' => null, 'product_attribute_id' => null, 'quantity' => 1],
        ];
    }

    protected function rules(): array
    {
        return [
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_attribute_id' => 'nullable|exists:product_attributes,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'product_attribute_id' => null, 'quantity' => 1];
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

        $transfer = StockTransfer::create([
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id' => $this->to_warehouse_id,
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'notes' => $this->notes,
        ]);

        foreach ($this->items as $item) {
            StockTransferItem::create([
                'stock_transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'product_attribute_id' => $item['product_attribute_id'] ?: null,
                'quantity' => (int) $item['quantity'],
            ]);
        }

        session()->flash('message', __('Stock transfer created successfully.'));
        $this->redirect(route('admin.stock-transfers.index'));
    }

    public function render()
    {
        return view('livewire.admin.stock-transfers.create', [
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name_en')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('New Stock Transfer'),
        ]);
    }
}
