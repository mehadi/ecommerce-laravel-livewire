<?php

namespace App\Livewire\Platform\Concerns;

use App\Models\Tenant;
use App\Models\TenantBillingEvent;
use App\Notifications\UpgradeRequestApproved;
use App\Notifications\UpgradeRequestRejected;

/**
 * Shared approve/reject logic for tenant upgrade requests, used by both
 * Platform\Tenants\Show (per-tenant) and Platform\UpgradeRequests\Index (the
 * cross-tenant queue) so the plan-swap behavior can't drift between the two.
 */
trait HandlesUpgradeRequests
{
    protected function approveTenantUpgrade(Tenant $tenant): void
    {
        $targetPlanId = $tenant->desired_plan_id ?? $tenant->plan_id;

        $tenant->update([
            'plan_id' => $targetPlanId,
            'desired_plan_id' => null,
            'upgrade_requested_at' => null,
        ]);

        $tenant = $tenant->fresh();

        $this->logTenantBillingEvent($tenant, 'upgrade_approved', note: 'Upgrade to '.($tenant->plan?->name ?? 'none').' approved.');

        $tenant->owner?->notify(new UpgradeRequestApproved($tenant));
    }

    protected function rejectTenantUpgrade(Tenant $tenant): void
    {
        $tenant->update(['desired_plan_id' => null, 'upgrade_requested_at' => null]);
        $this->logTenantBillingEvent($tenant, 'upgrade_rejected', note: 'Upgrade request rejected.');

        $tenant->owner?->notify(new UpgradeRequestRejected($tenant));
    }

    protected function logTenantBillingEvent(Tenant $tenant, string $type, ?float $amount = null, ?string $note = null): void
    {
        TenantBillingEvent::create([
            'tenant_id' => $tenant->id,
            'recorded_by' => auth()->id(),
            'type' => $type,
            'amount' => $amount,
            'note' => $note,
        ]);
    }
}
