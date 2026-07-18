<?php

namespace App\Http\Middleware;

use App\Support\Tenancy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantResolved
{
    /**
     * Guards tenant-only routes (storefront, dashboard, admin) from being reached
     * on the central/platform domain, where no tenant is bound and tenant-scoped
     * global scopes would otherwise no-op (returning unscoped, cross-tenant data).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Tenancy::check()) {
            $user = $request->user();

            // Authenticated platform staff (tenant_id null) have no tenant context, so
            // every tenant-only route (storefront, dashboard, admin) is unreachable for
            // them here — send them back to the platform area they do have access to
            // instead of a bare 404 that leaves them stuck.
            if ($user && $user->tenant_id === null) {
                session()->flash('error', __("That page isn't available here — you've been sent to the platform dashboard."));

                return redirect()->route('platform.dashboard');
            }

            abort(404);
        }

        return $next($request);
    }
}
