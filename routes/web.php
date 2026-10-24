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

Route::get('/dashboard', function () {
    $user = auth()->user();
    return $user->isAdmin()
        ? view('admin.dashboard')
        : view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
