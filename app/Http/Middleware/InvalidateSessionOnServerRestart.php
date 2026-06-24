<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InvalidateSessionOnServerRestart
{
    private static ?string $serverInstanceId = null;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasSession()) {
            return $next($request);
        }

        self::$serverInstanceId ??= (string) Str::uuid();

        $storedInstanceId = $request->session()->get('server_instance_id');

        if ($storedInstanceId !== null && $storedInstanceId !== self::$serverInstanceId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->session()->put('server_instance_id', self::$serverInstanceId);

        return $next($request);
    }
}
