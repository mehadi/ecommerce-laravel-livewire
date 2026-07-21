<?php

namespace App\Livewire\Admin\Pos\Shifts;

use App\Models\PosShift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $perPage = 15;

    public $sortField = 'opened_at';

    public $sortDirection = 'desc';

    protected $queryString = ['search', 'filterStatus', 'perPage', 'sortField', 'sortDirection'];

    public function mount(): void
    {
        Gate::authorize('view pos reports');
    }

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

    /**
     * A manager/admin remotely closing a cashier's shift can't physically
     * count the drawer, so this closes against the computed expected amount
     * (variance always 0) rather than prompting for a cash count.
     */
    public function forceCloseShift($shiftId): void
    {
        Gate::authorize('force close pos shift');

        $shift = PosShift::findOrFail($shiftId);

        if ($shift->status !== 'open') {
            return;
        }

        $movements = $shift->cashMovements()->get();
        $expected = round(
            $shift->opening_cash
            + $movements->whereIn('type', ['cash_in', 'sale_cash'])->sum('amount')
            - $movements->whereIn('type', ['cash_out', 'refund_cash'])->sum('amount'),
            2
        );

        $shift->update([
            'closed_by' => Auth::id(),
            'closing_cash' => $expected,
            'expected_cash' => $expected,
            'variance' => 0,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => __('Force-closed by :name (no physical cash count).', ['name' => Auth::user()->name]),
        ]);

        session()->flash('message', __('Shift force-closed.'));
    }

    protected function getShiftsQuery()
    {
        return PosShift::query()
            ->with(['register', 'openedBy', 'closedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('register', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                    ->orWhereHas('openedBy', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            });
    }

    public function render()
    {
        $shifts = $this->getShiftsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'open' => PosShift::where('status', 'open')->count(),
            'today' => PosShift::whereDate('opened_at', today())->count(),
            'variance_today' => PosShift::whereDate('closed_at', today())->sum('variance'),
        ];

        return view('livewire.admin.pos.shifts.index', [
            'shifts' => $shifts,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('POS Shifts'),
        ]);
    }
}
