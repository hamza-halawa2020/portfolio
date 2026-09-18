<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class ResolveAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);
        $request->session()->put('admin_locale', $locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $configuredLocales = config('app.supported_locales', ['en', 'ar']);
        $supportedLocales = is_array($configuredLocales)
            ? array_values(array_filter($configuredLocales, 'is_string'))
            : array_filter(explode(',', (string) $configuredLocales));
        $requestedLocale = $request->query('locale');

        if (is_string($requestedLocale) && in_array($requestedLocale, $supportedLocales, true)) {
            return $requestedLocale;
        }

        $sessionLocale = $request->session()->get('admin_locale');

        if (is_string($sessionLocale) && in_array($sessionLocale, $supportedLocales, true)) {
            return $sessionLocale;
        }

        $acceptedLocale = Arr::first($request->getLanguages(), fn (string $locale): bool => in_array($locale, $supportedLocales, true));

        return $acceptedLocale ?? (string) config('app.locale', 'en');
    }
}
