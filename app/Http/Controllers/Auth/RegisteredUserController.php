<?php

namespace App\Http\Controllers\Auth;

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ExpediteurRegisterRequest;
use App\Http\Requests\Auth\GroupeurRegisterRequest;
use App\Models\GroupeurProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the profile choice view (2 cartes : Expéditeur vs Groupeur).
     */
    public function create(): View
    {
        return view('auth.register-choice');
    }

    /**
     * Display the expediteur registration view.
     */
    public function createExpediteur(): View
    {
        return view('auth.register-expediteur');
    }

    /**
     * Handle an incoming expediteur registration request.
     */
    public function storeExpediteur(ExpediteurRegisterRequest $request): RedirectResponse
    {
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'locale' => session('locale', 'fr'),
        ]);

        $user->role = Role::expediteur;
        $user->status = 'actif';
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Display the groupeur registration view.
     */
    public function createGroupeur(): View
    {
        return view('auth.register-groupeur');
    }

    /**
     * Handle an incoming groupeur registration request.
     */
    public function storeGroupeur(GroupeurRegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            // Sauvegarder la pièce d'identité sur le disque privé local
            $path = $request->file('id_document')->store('id_documents', 'local');

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'locale' => session('locale', 'fr'),
            ]);

            $user->role = Role::groupeur;
            $user->status = 'actif';
            $user->save();

            GroupeurProfile::create([
                'user_id' => $user->id,
                'bio' => $request->bio,
                'usual_routes' => $request->usual_routes,
                'id_document_path' => $path,
                'validation_status' => GroupeurStatus::en_attente,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('groupeur.waiting');
    }
}
