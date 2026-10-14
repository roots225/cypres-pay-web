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
     * Only allow authenticated users with role 'admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter avec un compte Administrateur pour accéder à cet espace.');
        }

        if (! Auth::user()->isAdmin()) {
            // Utilisateur connecté mais sans rôle admin
            return redirect()->route('home')->with('error', 'Accès refusé : Le tableau de bord d\'administration est strictement réservé à la Direction Cypres Pay.');
        }

        return $next($request);
    }
}
