<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->isTeknisi()) {
            return redirect()->route('maintenance')->with('error', 'Akses ditolak. Fitur ini hanya dapat diakses oleh Admin.');
        }

        return redirect()->route('signin');
    }
}
