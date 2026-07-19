<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class Tenancy
{
    public static function current(): ?Tenant
    {
        return app()->bound('currentTenant') ? app('currentTenant') : null;
    }

    public static function id(): ?int
    {
        return static::current()?->id;
    }

    public static function check(): bool
    {
        return static::current() !== null;
    }

    /**
     * Namespace a cache key by the current tenant, so tenant-scoped Cache::remember/forget
     * calls can't leak or clobber another tenant's cached data.
     */
    public static function cacheKey(string $key): string
    {
        return $key.'.tenant-'.(static::id() ?? 'none');
    }

    /**
     * Namespace a storage path by the current tenant, so uploaded files
     * (product images, logos, etc.) can't collide or be served across tenants.
     */
    public static function storagePath(string $path): string
    {
        return 'tenants/'.(static::id() ?? 'central').'/'.ltrim($path, '/');
    }

    /**
     * Whether the current tenant's plan has a given feature flag enabled
     * (e.g. 'advanced_analytics_enabled'). False with no tenant resolved.
     */
    public static function hasFeature(string $key): bool
    {
        return static::current()?->hasFeature($key) ?? false;
    }

    /**
     * An absolute URL to the platform's own central domain (staff area, marketing
     * site), regardless of which host the current code path happens to be running
     * on — e.g. a notification triggered by a tenant-side action (like requesting
     * an upgrade) must link platform staff back to the CENTRAL domain, not
     * whichever tenant subdomain/custom domain the triggering request came in on.
     * Mirrors Tenant::primaryUrl()'s scheme/port handling for the same reason:
     * both hosts are served by the same app on the same scheme/port in practice.
     */
    public static function platformUrl(string $path = ''): string
    {
        $scheme = request()->getScheme();
        $port = request()->getPort();
        $portSuffix = in_array($port, [80, 443], true) ? '' : ':'.$port;

        $central = config('tenancy.central_domains')[0] ?? request()->getHost();

        return $scheme.'://'.$central.$portSuffix.$path;
    }

    /**
     * spatie/laravel-permission's teams mode auto-scopes Role creation and its own
     * findByName()/findById() helpers to the current team, but does NOT scope plain
     * Role::query()/all()/count() calls. This mirrors that same null-or-current-team
     * matching for the query paths that need it explicitly (listing, counting, syncing).
     */
    public static function roleQuery(): Builder
    {
        return Role::query()->where(
            fn ($query) => $query->whereNull('tenant_id')->orWhere('tenant_id', static::id())
        );
    }
}
