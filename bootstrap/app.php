<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \App\Http\Middleware\ResolveTenant::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'tenant' => \App\Http\Middleware\EnsureTenantResolved::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A tenant user without the 'access admin' permission hitting an /admin/*
        // route gets bounced to their dashboard instead of a bare 403 page — same
        // "send them where they do have access" treatment as EnsureTenantResolved
        // applies to platform staff hitting tenant-only routes.
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            $routeName = $request->route()?->getName();

            if ($request->expectsJson() || ! $request->user() || ! $routeName || ! str_starts_with($routeName, 'admin.')) {
                return null;
            }

            session()->flash('error', __("You don't have permission to access that page."));

            return redirect()->route('dashboard');
        });
    })->create();
