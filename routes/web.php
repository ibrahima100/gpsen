<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

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
});

// Espace Expéditeur
Route::middleware(['auth', 'role:expediteur'])->prefix('expediteur')->name('expediteur.')->group(function () {
    Route::get('/tableau-de-bord', fn () => view('expediteur.dashboard'))->name('dashboard');
});

// Espace Groupeur validé
Route::middleware(['auth', 'role:groupeur', 'groupeur.validated'])->prefix('groupeur')->name('groupeur.')->group(function () {
    Route::get('/tableau-de-bord', fn () => view('dashboard'))->name('dashboard');
});

// Espace Administrateur
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', fn () => view('admin.dashboard'))->name('dashboard');
});

// Profil utilisateur
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
