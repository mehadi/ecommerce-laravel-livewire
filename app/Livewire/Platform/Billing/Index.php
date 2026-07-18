<?php

namespace App\Livewire\Platform\Billing;

use App\Models\Tenant;
use App\Models\TenantBillingEvent;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $type = '';

    public ?int $tenantId = null;

    public ?string $from = null;

    public ?string $to = null;

    public int $perPage = 25;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = ['type', 'tenantId', 'from', 'to', 'perPage', 'sortField', 'sortDirection'];

    public function mount(): void
    {
        Gate::authorize('access platform');
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingTenantId(): void
    {
        $this->resetPage();
    }

    public function updatingFrom(): void
    {
        $this->resetPage();
    }

    public function updatingTo(): void
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

    protected function baseQuery()
    {
        return TenantBillingEvent::query()
            ->with(['tenant', 'recordedBy'])
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->tenantId, fn ($q) => $q->where('tenant_id', $this->tenantId))
            ->when($this->from, fn ($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('created_at', '<=', $this->to));
    }

    public function export()
    {
        Gate::authorize('access platform');

        $events = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->get();

        return response()->streamDownload(function () use ($events) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Tenant', 'Type', 'Amount', 'Note', 'Recorded By']);

            foreach ($events as $event) {
                fputcsv($handle, [
                    $event->created_at->format('Y-m-d H:i'),
                    $event->tenant?->name,
                    $event->type,
                    $event->amount,
                    $event->note,
                    $event->recordedBy?->name,
                ]);
            }

            fclose($handle);
        }, 'billing-events-'.now()->format('Y-m-d').'.csv');
    }

    public function render()
    {
        $summary = [
            'total_revenue' => (float) $this->baseQuery()->where('type', 'payment_recorded')->sum('amount'),
            'by_type' => $this->baseQuery()->selectRaw('type, count(*) as count')->groupBy('type')->pluck('count', 'type'),
        ];

        $events = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.platform.billing.index', [
            'events' => $events,
            'summary' => $summary,
            'tenants' => Tenant::orderBy('name')->get(['id', 'name']),
        ])->layout('components.layouts.app', [
            'title' => __('Billing'),
        ]);
    }
}
