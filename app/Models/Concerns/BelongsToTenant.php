<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if ($model->tenant_id !== null) {
                return;
            }

            if (! Tenancy::check()) {
                // Console context (migrations/seeders/tinker) without a bound tenant:
                // the caller is responsible for setting tenant_id explicitly.
                if (! app()->runningInConsole()) {
                    throw new RuntimeException(sprintf(
                        'Cannot create a [%s] without a resolved tenant.',
                        static::class
                    ));
                }

                return;
            }

            $model->tenant_id = Tenancy::id();
        });
    }

    /**
     * Escape hatch for legitimate cross-tenant queries (platform-admin views).
     * Any use of this should be easy to grep for and explicit.
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
