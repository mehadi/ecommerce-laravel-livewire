<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Platform-staff security audit trail (impersonation start/stop, etc.). Kept
 * separate from TenantBillingEvent, which is billing/subscription history that
 * may be tenant-owner-facing — this table is platform-internal only.
 */
class AdminAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'action',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
