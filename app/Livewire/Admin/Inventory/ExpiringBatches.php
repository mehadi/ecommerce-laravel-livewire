<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\ProductBatch;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ExpiringBatches extends Component
{
    public $filterStatus = ''; // '', 'expired', 'expiring_soon'

    public function mount(): void
    {
        Gate::authorize('view inventory');
    }

    public function render()
    {
        $batches = ProductBatch::with(['product', 'warehouse'])
            ->whereNotNull('expires_at')
            ->where('quantity', '>', 0)
            ->when($this->filterStatus === 'expired', fn ($query) => $query->where('expires_at', '<', now()))
            ->when($this->filterStatus === 'expiring_soon', fn ($query) => $query->where('expires_at', '>=', now())->where('expires_at', '<=', now()->addDays(30)))
            ->when($this->filterStatus === '', fn ($query) => $query->where('expires_at', '<=', now()->addDays(30)))
            ->orderBy('expires_at')
            ->get();

        return view('livewire.admin.inventory.expiring-batches', [
            'batches' => $batches,
        ])->layout('components.layouts.app', [
            'title' => __('Expiring Batches'),
        ]);
    }
}
