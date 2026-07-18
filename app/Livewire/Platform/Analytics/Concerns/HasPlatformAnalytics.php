<?php

namespace App\Livewire\Platform\Analytics\Concerns;

use App\Models\Tenant;
use App\Models\TenantBillingEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

/**
 * System-wide analytics for the Platform admin, mirroring the date-bucketing
 * pattern from App\Livewire\Dashboard\Concerns\HasDashboardAnalytics (daily
 * <=31 days / weekly <=93 days / monthly beyond), reused here for tenant
 * growth, payments, and churn instead of orders.
 */
trait HasPlatformAnalytics
{
    protected function rangeStart(): Carbon
    {
        return $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDays(90);
    }

    protected function rangeEnd(): Carbon
    {
        return $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();
    }

    /**
     * Plan-price-based MRR estimate — sum of active tenants' current plan
     * price, normalized to a monthly figure (yearly plans contribute
     * price/12 — see Plan::monthlyPrice()). Distinct from
     * paymentsOverTimeData(), which reflects amounts actually recorded as
     * received.
     */
    #[Computed]
    public function mrr(): float
    {
        return (float) Tenant::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(fn (Tenant $tenant) => $tenant->plan?->monthlyPrice() ?? 0);
    }

    #[Computed]
    public function planBreakdownData(): array
    {
        $grouped = Tenant::where('status', 'active')
            ->with('plan')
            ->get()
            ->groupBy(fn (Tenant $tenant) => $tenant->plan?->name ?? __('No Plan'));

        return [
            'labels' => $grouped->keys()->toArray(),
            'tenant_counts' => $grouped->map->count()->values()->toArray(),
            'mrr' => $grouped->map(fn (Collection $group) => (float) $group->sum(fn (Tenant $tenant) => $tenant->plan?->monthlyPrice() ?? 0))->values()->toArray(),
        ];
    }

    #[Computed]
    public function tenantGrowthChartData(): array
    {
        $tenants = Tenant::whereBetween('created_at', [$this->rangeStart(), $this->rangeEnd()])->get(['id', 'created_at']);

        return $this->bucketedSeries($tenants, 'created_at', fn (Collection $group) => $group->count());
    }

    /**
     * Actual recorded revenue over time (payment_recorded events), shown
     * alongside the plan-price mrr() estimate above.
     */
    #[Computed]
    public function paymentsOverTimeData(): array
    {
        $events = TenantBillingEvent::where('type', 'payment_recorded')
            ->whereBetween('created_at', [$this->rangeStart(), $this->rangeEnd()])
            ->get(['amount', 'created_at']);

        return $this->bucketedSeries($events, 'created_at', fn (Collection $group) => (float) $group->sum(fn (TenantBillingEvent $event) => (float) $event->amount));
    }

    /**
     * v1 approximation: counts 'suspended' events plus 'status_changed'
     * events whose note mentions cancellation. No dedicated cancelled_at
     * timestamp exists yet, so this is a best-effort signal, not exact.
     */
    #[Computed]
    public function churnData(): array
    {
        $events = TenantBillingEvent::whereIn('type', ['suspended', 'status_changed'])
            ->where(function ($query) {
                $query->where('type', 'suspended')
                    ->orWhere('note', 'like', '%cancelled%');
            })
            ->whereBetween('created_at', [$this->rangeStart(), $this->rangeEnd()])
            ->get(['created_at']);

        return $this->bucketedSeries($events, 'created_at', fn (Collection $group) => $group->count());
    }

    #[Computed]
    public function chartDataBundle(): array
    {
        return [
            'platform_growth_chart' => $this->tenantGrowthChartData,
            'plan_breakdown_chart' => $this->planBreakdownData,
            'platform_payments_chart' => $this->paymentsOverTimeData,
            'platform_churn_chart' => $this->churnData,
        ];
    }

    protected function bucketedSeries(Collection $items, string $dateField, \Closure $aggregate): array
    {
        $start = $this->rangeStart();
        $end = $this->rangeEnd();
        $daysDiff = $start->diffInDays($end);

        $labels = [];
        $data = [];

        if ($daysDiff <= 31) {
            $current = $start->copy();
            while ($current->lte($end)) {
                $labels[] = $current->format('M d');
                $data[] = $aggregate($items->filter(fn ($item) => Carbon::parse($item->{$dateField})->isSameDay($current)));
                $current->addDay();
            }
        } elseif ($daysDiff <= 93) {
            $current = $start->copy()->startOfWeek();
            while ($current->lte($end)) {
                $weekEnd = $current->copy()->endOfWeek();
                if ($weekEnd->gt($end)) {
                    $weekEnd = $end->copy();
                }
                $labels[] = $current->format('M d').' - '.$weekEnd->format('M d');
                $data[] = $aggregate($items->filter(function ($item) use ($dateField, $current, $weekEnd) {
                    $date = Carbon::parse($item->{$dateField});

                    return $date->gte($current) && $date->lte($weekEnd);
                }));
                $current->addWeek();
            }
        } else {
            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $monthEnd = $current->copy()->endOfMonth();
                if ($monthEnd->gt($end)) {
                    $monthEnd = $end->copy();
                }
                $labels[] = $current->format('M Y');
                $data[] = $aggregate($items->filter(function ($item) use ($dateField, $current, $monthEnd) {
                    $date = Carbon::parse($item->{$dateField});

                    return $date->gte($current) && $date->lte($monthEnd);
                }));
                $current->addMonth();
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
