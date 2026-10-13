<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // Un employé peut accéder aux routes prestataire (ses droits sont filtrés dans la vue)
        $allowed = $userRole === $role
            || ($userRole === 'employe' && $role === 'prestataire');

        if (! $allowed) {
            $dashboard = match ($userRole) {
                'admin'                 => 'admin.index',
                'prestataire', 'employe'=> 'prestataire.index',
                default                 => 'client.index',
            };
            return redirect()->route($dashboard)
                ->with('error', 'Accès non autorisé à cette section.');
        }

        return $next($request);
    }
}
