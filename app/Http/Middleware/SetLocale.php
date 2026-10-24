<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     *
     * @var array<string>
     */
    protected const SUPPORTED_LOCALES = ['fr', 'en'];

    /**
     * Default locale.
     */
    protected const DEFAULT_LOCALE = 'fr';

    /**
     * Handle an incoming request.
     *
     * Determines current locale from session or authenticated user's preference,
     * falls back to 'fr', and configures the application locale.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = self::DEFAULT_LOCALE;

        if ($request->hasSession() && $request->session()->has('locale')) {
            $candidate = (string) $request->session()->get('locale');
            if (in_array($candidate, self::SUPPORTED_LOCALES, true)) {
                $locale = $candidate;
            }
        } elseif ($user = $request->user()) {
            $candidate = (string) ($user->locale ?? '');
            if (in_array($candidate, self::SUPPORTED_LOCALES, true)) {
                $locale = $candidate;
                if ($request->hasSession()) {
                    $request->session()->put('locale', $locale);
                }
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
