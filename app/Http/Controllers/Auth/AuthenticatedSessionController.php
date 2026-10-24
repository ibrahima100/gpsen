<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Vérifier si le compte est suspendu
        if ($user->status === 'suspendu') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => __('app.account_suspended', ['reason' => $user->suspension_reason ? " Mot de suspension : {$user->suspension_reason}" : '']),
            ]);
        }

        $request->session()->regenerate();

        // Redirection contextuelle selon le rôle
        return match ($user->role) {
            Role::admin => redirect()->intended(route('admin.dashboard', absolute: false)),
            Role::groupeur => $user->isGroupeurValidated()
                ? redirect()->intended(route('groupeur.dashboard', absolute: false))
                : redirect()->route('groupeur.waiting'),
            default => redirect()->intended(route('dashboard', absolute: false)),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
