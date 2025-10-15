<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->locale) {
            app()->setLocale($request->user()->locale);
        } else {
            app()->setLocale($request->getPreferredLanguage());
        }

        return $next($request);
    }
}
