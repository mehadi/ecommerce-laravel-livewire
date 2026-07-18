<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Platform staff (tenant_id null) have no resolved tenant, so the default
     * "/dashboard" home route — which lives inside the tenant-only route group —
     * would 404 for them. Send them to the platform admin area instead.
     *
     * redirect()->intended() would happily override that with whatever URL is
     * sitting in session('url.intended') — e.g. a platform user who was bounced
     * to login from a tenant-only URL like "/dashboard" would land right back on
     * the 404 it came from. Only honor the intended URL when it's in the same
     * area (platform vs tenant) as the user who just logged in.
     */
    public function toResponse($request)
    {
        $isPlatform = $request->user()?->tenant_id === null;
        $home = $isPlatform ? route('platform.dashboard') : route('dashboard');

        $intended = $request->session()->pull('url.intended');

        if ($intended && $this->intendedMatchesArea($intended, $isPlatform)) {
            return redirect()->to($intended);
        }

        return redirect()->to($home);
    }

    private function intendedMatchesArea(string $intended, bool $isPlatform): bool
    {
        $path = ltrim((string) parse_url($intended, PHP_URL_PATH), '/');

        return Str::startsWith($path, 'platform') === $isPlatform;
    }
}
