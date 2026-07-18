<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $tenant = $this->resolveViaDomain($host) ?? $this->resolveViaSubdomain($host);

        if ($tenant === null && ! $this->isCentralDomain($host)) {
            abort(404);
        }

        if ($tenant !== null) {
            if (! $tenant->isActive()) {
                if ($tenant->status === 'suspended') {
                    abort(response()->view('errors.tenant-suspended', [], 403));
                }

                // Any other inactive status (e.g. cancelled) stays indistinguishable
                // from "never existed" so a cancelled tenant's slug/domain can't be
                // probed to confirm it once existed.
                abort(404);
            }

            app()->instance('currentTenant', $tenant);
        }

        // Scopes spatie/laravel-permission role/permission checks to this tenant
        // (null on the central domain, restricting checks there to global/team-less roles).
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant?->id);

        return $next($request);
    }

    protected function resolveViaDomain(string $host): ?Tenant
    {
        return Domain::whereNotNull('verified_at')
            ->where('domain', $host)
            ->first()
            ?->tenant;
    }

    protected function resolveViaSubdomain(string $host): ?Tenant
    {
        foreach (config('tenancy.central_domains', []) as $central) {
            $suffix = '.'.$central;

            if (str_ends_with($host, $suffix)) {
                $slug = substr($host, 0, -strlen($suffix));

                return Tenant::where('slug', $slug)->first();
            }
        }

        return null;
    }

    protected function isCentralDomain(string $host): bool
    {
        return in_array($host, config('tenancy.central_domains', []), true);
    }
}
