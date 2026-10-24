<?php

namespace App\Http\Middleware;

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroupeurValidated
{
    /**
     * Handle an incoming request.
     *
     * Ensures that the authenticated groupeur has been officially validated
     * by an administrator before accessing restricted groupeur actions (e.g. publishing trips).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Must be authenticated
        if (! $user) {
            return redirect()->guest('/connexion');
        }

        $userRoleValue = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        // 2. Administrators bypass validation check
        if ($userRoleValue === Role::ADMIN->value) {
            return $next($request);
        }

        // 3. User must be a groupeur
        if ($userRoleValue !== Role::GROUPEUR->value) {
            abort(Response::HTTP_FORBIDDEN, __('Accès réservé aux transporteurs/groupeurs.'));
        }

        // 4. Check groupeur profile validation status
        $profile = $user->groupeurProfile;
        $status = $profile?->validation_status;
        $statusValue = $status instanceof GroupeurStatus ? $status->value : (string) $status;

        if ($statusValue !== GroupeurStatus::VALIDE->value) {
            return redirect('/groupeur/attente-validation');
        }

        return $next($request);
    }
}
