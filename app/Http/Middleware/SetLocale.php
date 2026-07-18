<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Routes that should always use the default application locale.
     *
     * @var array<int, string>
     */
    protected array $backendPatterns = [
        'admin',
        'admin/*',
        'dashboard',
        'dashboard/*',
        'settings',
        'settings/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is($this->backendPatterns)) {
            app()->setLocale(config('app.locale'));

            return $next($request);
        }

        $locale = $request->get('lang')
            ?? session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        if (in_array($locale, ['en', 'bn'])) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
