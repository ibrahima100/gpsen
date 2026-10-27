<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotSuspended
{
    /**
     * Handle an incoming request.
     * Si l'utilisateur connecté est suspendu, déconnecter et bloquer immédiatement.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && isset($user->status) && $user->status === 'suspendu') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été suspendu par l\'équipe GPsen. Veuillez contacter le support pour plus d\'informations.',
            ]);
        }

        return $next($request);
    }
}
