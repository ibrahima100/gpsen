<?php

use App\Http\Controllers\Admin\GroupeurValidationController;
use App\Http\Controllers\Expediteur\ShipmentRequestController;
use App\Http\Controllers\Groupeur\TripController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicTripController;
use Illuminate\Support\Facades\Route;

// Zone publique (Visiteur / Tous)
Route::get('/', [PublicTripController::class, 'home'])->name('home');
Route::get('/trajets', [PublicTripController::class, 'index'])->name('trajets.index');
Route::get('/trajets/{trip}', [PublicTripController::class, 'show'])->name('trajets.show');
Route::view('/comment-ca-marche', 'pages.how-it-works')->name('how-it-works');

// Changement de langue (FR / EN)
Route::get('/langue/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fr', 'en'], true), 404);
    session(['locale' => $locale]);
    if ($user = auth()->user()) {
        $user->forceFill(['locale' => $locale])->save();
    }
    return back();
})->name('locale.switch');

// Redirection intelligente du dashboard Breeze
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isGroupeur()) {
        return $user->isGroupeurValidated()
            ? redirect()->route('groupeur.dashboard')
            : redirect()->route('groupeur.waiting');
    }
    return redirect()->route('expediteur.dashboard');
})->middleware(['auth'])->name('dashboard');

// Espace Groupeur en attente de validation
Route::middleware(['auth', 'role:groupeur'])->group(function () {
    Route::get('/groupeur/attente-validation', function () {
        $user = auth()->user();
        if ($user->isGroupeurValidated()) {
            return redirect()->route('groupeur.dashboard');
        }
        return view('groupeur.waiting', [
            'profile' => $user->groupeurProfile,
        ]);
    })->name('groupeur.waiting');

    Route::post('/groupeur/nouvelle-soumission', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('id_document')->store('id_documents', 'local');
        $user = auth()->user();
        $user->groupeurProfile()->update([
            'id_document_path' => $path,
            'validation_status' => \App\Enums\GroupeurStatus::en_attente,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Votre nouveau document a bien été enregistré. Il est de nouveau en cours d\'examen.');
    })->name('groupeur.resubmit');
});

// Espace Expéditeur
Route::middleware(['auth', 'role:expediteur'])->prefix('expediteur')->name('expediteur.')->group(function () {
    Route::get('/tableau-de-bord', fn () => view('expediteur.dashboard'))->name('dashboard');

    // Demandes d'envoi de colis
    Route::get('/trajets/{trip}/demande', [ShipmentRequestController::class, 'create'])->name('requests.create');
    Route::post('/trajets/{trip}/demande', [ShipmentRequestController::class, 'store'])->name('requests.store');
});

// Espace Groupeur validé
Route::middleware(['auth', 'role:groupeur', 'groupeur.validated'])->prefix('groupeur')->name('groupeur.')->group(function () {
    Route::get('/tableau-de-bord', function () {
        $user = auth()->user();
        $activeTripsCount = $user->trips()->whereIn('status', ['ouvert', 'complet', 'parti'])->count();
        return view('groupeur.dashboard', [
            'user' => $user,
            'activeTripsCount' => $activeTripsCount,
        ]);
    })->name('dashboard');

    // CRUD Trajets groupeur (alias en français /nouveau)
    Route::get('trajets/nouveau', [TripController::class, 'create'])->name('trips.nouveau');
    Route::resource('trajets', TripController::class)->parameters(['trajets' => 'trip'])->names('trips')->except(['show', 'destroy']);
    Route::post('trajets/{trip}/annuler', [TripController::class, 'cancel'])->name('trips.cancel');
});

// Espace Administrateur
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', function () {
        $pendingGroupeurs = \App\Models\GroupeurProfile::where('validation_status', 'en_attente')->count();
        $totalUsers = \App\Models\User::count();
        $openTrips = \App\Models\Trip::where('status', 'ouvert')->count();
        return view('admin.dashboard', compact('pendingGroupeurs', 'totalUsers', 'openTrips'));
    })->name('dashboard');

    // File de validation des pièces d'identité groupeurs
    Route::get('/groupeurs', [GroupeurValidationController::class, 'index'])->name('groupeurs.index');
    Route::get('/groupeurs/{profile}', [GroupeurValidationController::class, 'show'])->name('groupeurs.show');
    Route::get('/groupeurs/{profile}/piece-identite', [GroupeurValidationController::class, 'viewDocument'])->name('groupeurs.document');
    Route::post('/groupeurs/{profile}/valider', [GroupeurValidationController::class, 'validateProfile'])->name('groupeurs.validate');
    Route::post('/groupeurs/{profile}/refuser', [GroupeurValidationController::class, 'rejectProfile'])->name('groupeurs.reject');
});

// Profil utilisateur
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
