<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            $user->two_fa_enabled &&
            ! $request->session()->get('2fa_verified') &&
            ! $request->routeIs('2fa.*') &&
            ! $request->routeIs('logout')
        ) {
            $request->session()->put('2fa_user_id', $user->id);
            Auth::logout();
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
