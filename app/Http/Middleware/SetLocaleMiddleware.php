<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // For authenticated users, use their saved preference
        if ($request->user() && $request->user()->locale) {
            $locale = $request->user()->locale;
            app()->setLocale($locale);
            session(['locale' => $locale]);
        } 
        // For guest users, check query parameter, then session, then browser preference
        else {
            $locale = $request->input('locale') 
                ?? session('locale') 
                ?? $request->getPreferredLanguage();
            
            // Store in session for guest users
            if ($request->has('locale')) {
                session(['locale' => $locale]);
            }
            
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
