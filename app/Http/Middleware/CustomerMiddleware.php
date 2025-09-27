<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        if (!auth()->guard('customer')->user()->is_active) {
            auth()->guard('customer')->logout();
            return redirect()->route('login')
                             ->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
