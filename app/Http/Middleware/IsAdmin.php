<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        if (auth()->check()) {
            return redirect()->route('user.dashboard')->with('error', 'Halaman ini khusus admin.');
        }

        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }
}
