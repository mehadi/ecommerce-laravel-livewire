<?php

namespace App\Livewire\Admin\Coupons;

use App\Models\Coupon;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $showModal = false;

    public $editingId = null;

    public $search = '';

    public $filterStatus = '';

    public $filterType = '';

    public $filterValidity = '';

    public $perPage = 15;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $code = '';

    public $name = '';

    public $description = '';

    public $type = 'percentage';

    public $value = 0;

    public $minimum_amount;

    public $usage_limit;

    public $starts_at;

    public $expires_at;

    public $is_active = true;

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterStatus', 'filterType', 'filterValidity', 'perPage', 'sortField', 'sortDirection'];

    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:255|unique:coupons,code,'.$this->editingId,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterValidity(): void
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
            $this->selectedItems = $this->getCouponsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function createCoupon(): void
    {
        $this->reset(['editingId', 'code', 'name', 'description', 'type', 'value', 'minimum_amount', 'usage_limit', 'starts_at', 'expires_at', 'is_active']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function editCoupon(Coupon $coupon): void
    {
        $this->editingId = $coupon->id;
        $this->code = $coupon->code;
        $this->name = $coupon->name;
        $this->description = $coupon->description;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->minimum_amount = $coupon->minimum_amount;
        $this->usage_limit = $coupon->usage_limit;
        $this->starts_at = $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : null;
        $this->expires_at = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : null;
        $this->is_active = $coupon->is_active;
        $this->showModal = true;
    }

    public function storeCoupon(): void
    {
        $this->validate();

        Coupon::create([
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount,
            'usage_limit' => $this->usage_limit,
            'starts_at' => $this->starts_at ? date('Y-m-d H:i:s', strtotime($this->starts_at)) : null,
            'expires_at' => $this->expires_at ? date('Y-m-d H:i:s', strtotime($this->expires_at)) : null,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Coupon created successfully.'));
        $this->showModal = false;
    }

    public function updateCoupon(): void
    {
        $this->validate();

        $coupon = Coupon::findOrFail($this->editingId);
        $coupon->update([
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount,
            'usage_limit' => $this->usage_limit,
            'starts_at' => $this->starts_at ? date('Y-m-d H:i:s', strtotime($this->starts_at)) : null,
            'expires_at' => $this->expires_at ? date('Y-m-d H:i:s', strtotime($this->expires_at)) : null,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Coupon updated successfully.'));
        $this->showModal = false;
    }

    public function deleteCoupon($couponId): void
    {
        Coupon::destroy($couponId);
        session()->flash('message', __('Coupon deleted successfully.'));
    }

    public function toggleStatus($couponId): void
    {
        $coupon = Coupon::findOrFail($couponId);
        $coupon->update(['is_active' => ! $coupon->is_active]);
        session()->flash('message', __('Coupon status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$couponId]);
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one coupon.'));

            return;
        }

        $coupons = Coupon::whereIn('id', $this->selectedItems)->get();
        $newStatus = $coupons->first()->is_active ? false : true;

        foreach ($coupons as $coupon) {
            $coupon->update(['is_active' => $newStatus]);
        }

        session()->flash('message', __(':count coupon(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one coupon.'));

            return;
        }

        Coupon::whereIn('id', $this->selectedItems)->delete();
        session()->flash('message', __(':count coupon(s) deleted successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function duplicate($couponId): void
    {
        $original = Coupon::findOrFail($couponId);

        $newCoupon = $original->replicate();
        $newCoupon->code = $this->generateUniqueCode($original->code.'-COPY');
        $newCoupon->name = $original->name.' (Copy)';
        $newCoupon->is_active = false;
        $newCoupon->used_count = 0;
        $newCoupon->save();

        session()->flash('message', __('Coupon duplicated successfully.'));
    }

    protected function generateUniqueCode(string $baseCode): string
    {
        $code = strtoupper($baseCode);
        $counter = 1;

        while (Coupon::where('code', $code)->exists()) {
            $code = strtoupper($baseCode).'-'.$counter;
            $counter++;
        }

        return $code;
    }

    protected function getCouponsQuery()
    {
        return Coupon::query()
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%'.$this->search.'%')
                    ->orWhere('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterType !== '', function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterValidity !== '', function ($query) {
                $now = Carbon::now();
                if ($this->filterValidity === 'valid') {
                    $query->where('is_active', true)
                        ->where(function ($q) use ($now) {
                            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($q) use ($now) {
                            $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                        })
                        ->where(function ($q) {
                            $q->whereNull('usage_limit')
                                ->orWhereRaw('used_count < usage_limit');
                        });
                } elseif ($this->filterValidity === 'expired') {
                    $query->where(function ($q) use ($now) {
                        $q->where('expires_at', '<', $now)
                            ->orWhere(function ($query) {
                                $query->whereNotNull('usage_limit')
                                    ->whereRaw('used_count >= usage_limit');
                            });
                    });
                } elseif ($this->filterValidity === 'expiring_soon') {
                    $query->where('expires_at', '>=', $now)
                        ->where('expires_at', '<=', $now->copy()->addDays(7));
                }
            });
    }

    public function render()
    {
        $coupons = $this->getCouponsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $now = Carbon::now();
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->count(),
            'inactive' => Coupon::where('is_active', false)->count(),
            'expired' => Coupon::where(function ($q) use ($now) {
                $q->where('expires_at', '<', $now)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('usage_limit')
                            ->whereRaw('used_count >= usage_limit');
                    });
            })->count(),
            'expiring_soon' => Coupon::where('expires_at', '>=', $now)
                ->where('expires_at', '<=', $now->copy()->addDays(7))
                ->where('is_active', true)
                ->count(),
        ];

        return view('livewire.admin.coupons.index', [
            'coupons' => $coupons,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Coupons'),
        ]);
    }
}
