<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // If the user isn't logged in, or their role doesn't match what is required
        if (!auth()->check() || auth()->user()->role !== $role) {
            // Redirect them safely back to the basic dashboard with an error flash message
            return redirect('/dashboard')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
