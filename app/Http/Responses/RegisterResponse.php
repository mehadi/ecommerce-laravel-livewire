<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * By the time this runs, Fortify has already logged the new user into the
     * CURRENT (central-domain) session — harmless, since a tenant-bound user
     * fails the Gate::define('access platform') check either way. Session
     * cookies are host-only (SESSION_DOMAIN=null, see Tenants\Show::impersonate()
     * for why), so that central-domain session doesn't carry over to the tenant's
     * own subdomain/custom domain — send them there to log in fresh instead.
     */
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->tenant_id === null) {
            return redirect()->route('platform.dashboard');
        }

        return redirect()->to($user->tenant->primaryUrl().'/login?welcome=1');
    }
}
