<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * The roles permitted to access the resource.
     *
     * @var array<int, Role|string>
     */
    protected array $roles;

    /**
     * Create a new middleware instance.
     *
     * @param  Role|string  ...$roles
     */
    public function __construct(Role|string ...$roles)
    {
        $this->roles = $roles;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  Role|string  ...$roles
     */
    public function handle(Request $request, Closure $next, Role|string ...$roles): Response
    {
        $user = $request->user();

        // 1. Must be authenticated
        if (! $user) {
            return redirect()->guest('/connexion');
        }

        // 2. Reject suspended accounts immediately
        if (isset($user->status) && $user->status === 'suspendu') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/connexion')->withErrors([
                'email' => __('app.account_suspended'),
            ]);
        }

        // 3. Resolve allowed roles from handle parameters or constructor arguments
        $allowedRoles = ! empty($roles) ? $roles : $this->roles;

        // If no roles specified, allow any authenticated active user
        if (empty($allowedRoles)) {
            return $next($request);
        }

        // Extract user's role string value
        $userRoleValue = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        // Check against permitted roles
        $hasRole = false;
        foreach ($allowedRoles as $role) {
            $roleValue = $role instanceof Role ? $role->value : (string) $role;
            if ($userRoleValue === $roleValue) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            abort(Response::HTTP_FORBIDDEN, __('Accès refusé pour ce profil.'));
        }

        return $next($request);
    }
}
