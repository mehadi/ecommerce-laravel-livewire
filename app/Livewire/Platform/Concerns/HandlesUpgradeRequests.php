<?php

namespace App\Livewire\Platform\Concerns;

use App\Models\Tenant;
use App\Models\TenantBillingEvent;

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

        $this->logTenantBillingEvent($tenant, 'upgrade_approved', note: 'Upgrade to '.($tenant->fresh()->plan?->name ?? 'none').' approved.');
    }

    protected function rejectTenantUpgrade(Tenant $tenant): void
    {
        $tenant->update(['desired_plan_id' => null, 'upgrade_requested_at' => null]);
        $this->logTenantBillingEvent($tenant, 'upgrade_rejected', note: 'Upgrade request rejected.');
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
