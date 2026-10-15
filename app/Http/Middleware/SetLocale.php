<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set active application locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['bn', 'en'];

        // 1. Direct query parameter override (e.g. ?lang=bn or ?lang=en)
        if ($request->has('lang') && in_array($request->query('lang'), $supportedLocales, true)) {
            $locale = $request->query('lang');
            Session::put('locale', $locale);
            if ($request->user() && $request->user()->locale !== $locale) {
                $request->user()->forceFill(['locale' => $locale])->saveQuietly();
            }
        } elseif (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales, true)) {
            $locale = Session::get('locale');
        } elseif ($request->hasCookie('locale') && in_array($request->cookie('locale'), $supportedLocales, true)) {
            $locale = $request->cookie('locale');
            Session::put('locale', $locale);
        } elseif ($request->user() && in_array($request->user()->locale ?? '', $supportedLocales, true)) {
            $locale = $request->user()->locale;
            Session::put('locale', $locale);
        } else {
            // Default to Bangla per client requirement
            $locale = config('app.locale', 'bn');
        }

        App::setLocale($locale);

        $response = $next($request);

        // Attach locale cookie (valid for 1 year) if not already set
        if (!$request->hasCookie('locale') || $request->cookie('locale') !== $locale) {
            $response->headers->setCookie(cookie('locale', $locale, 60 * 24 * 365));
        }

        return $response;
    }
}
