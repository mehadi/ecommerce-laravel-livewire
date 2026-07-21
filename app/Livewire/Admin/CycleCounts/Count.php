<?php

namespace App\Livewire\Admin\CycleCounts;

use App\Enums\StockMovementType;
use App\Models\CycleCount;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Livewire\Component;

class Count extends Component
{
    public CycleCount $cycleCount;

    /** @var array<int, int|null> cycle_count_item id => counted quantity */
    public array $countedQuantities = [];

    public function mount(CycleCount $cycleCount): void
    {
        $this->cycleCount = $cycleCount->load(['items.product', 'items.productAttribute', 'warehouse']);
        $this->countedQuantities = $this->cycleCount->items
            ->mapWithKeys(fn ($item) => [$item->id => $item->counted_quantity])
            ->all();
    }

    public function saveProgress(): void
    {
        $this->validate([
            'countedQuantities.*' => 'nullable|integer|min:0',
        ]);

        foreach ($this->cycleCount->items as $item) {
            $counted = $this->countedQuantities[$item->id] ?? null;

            if ($counted === null || $counted === '') {
                continue;
            }

            $item->update([
                'counted_quantity' => (int) $counted,
                'counted_by' => auth()->id(),
                'counted_at' => now(),
            ]);
        }

        if ($this->cycleCount->status === 'pending') {
            $this->cycleCount->update(['status' => 'in_progress']);
        }

        session()->flash('message', __('Count progress saved.'));
    }

    public function completeCount(): void
    {
        $this->validate([
            'countedQuantities.*' => 'nullable|integer|min:0',
        ]);

        $this->saveProgress();
        $this->cycleCount->refresh();

        foreach ($this->cycleCount->items as $item) {
            if ($item->counted_quantity === null || $item->counted_quantity === $item->expected_quantity) {
                continue;
            }

            $warehouseStock = WarehouseStock::findOrCreateFor(
                $this->cycleCount->warehouse_id,
                $item->product_id,
                $item->product_attribute_id
            );

            StockMovementContext::run([
                'type' => StockMovementType::CycleCount,
                'reason' => "Cycle count #{$this->cycleCount->id} reconciliation",
                'changed_by' => auth()->id(),
            ], function () use ($warehouseStock, $item) {
                $warehouseStock->update(['stock' => $item->counted_quantity]);
            });
        }

        $this->cycleCount->update(['status' => 'completed', 'completed_at' => now()]);

        session()->flash('message', __('Cycle count completed and discrepancies reconciled.'));
        $this->redirect(route('admin.cycle-counts.index'));
    }

    public function render()
    {
        return view('livewire.admin.cycle-counts.count')->layout('components.layouts.app', [
            'title' => __('Count'),
        ]);
    }
}
