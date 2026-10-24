<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GroupeurStatus;
use App\Http\Controllers\Controller;
use App\Models\GroupeurProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupeurValidationController extends Controller
{
    /**
     * Liste des dossiers groupeurs (en attente en priorité).
     */
    public function index(): View
    {
        $profiles = GroupeurProfile::with('user')
            ->orderByRaw("CASE WHEN validation_status = 'en_attente' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15);

        $pendingCount = GroupeurProfile::where('validation_status', GroupeurStatus::en_attente)->count();

        return view('admin.groupeurs.index', compact('profiles', 'pendingCount'));
    }

    /**
     * Fiche détaillée d'un dossier groupeur.
     */
    public function show(GroupeurProfile $profile): View
    {
        $profile->load('user', 'validator');
        return view('admin.groupeurs.show', compact('profile'));
    }

    /**
     * Visualisation sécurisée de la pièce d'identité (stockage privé).
     */
    public function viewDocument(GroupeurProfile $profile): StreamedResponse
    {
        if (! Storage::disk('local')->exists($profile->id_document_path)) {
            abort(404, 'Document introuvable sur le disque privé.');
        }

        return Storage::disk('local')->response(
            $profile->id_document_path,
            'piece_identite_' . $profile->user_id . '.' . pathinfo($profile->id_document_path, PATHINFO_EXTENSION),
            ['Content-Disposition' => 'inline']
        );
    }

    /**
     * Valider un groupeur.
     */
    public function validateProfile(Request $request, GroupeurProfile $profile): RedirectResponse
    {
        $profile->update([
            'validation_status' => GroupeurStatus::valide,
            'rejection_reason' => null,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Notification groupeur validé (Canal BDD Laravel)
        $profile->user->notify(new \App\Notifications\GroupeurValidatedNotification());

        return redirect()->route('admin.groupeurs.show', $profile)
            ->with('success', "Le groupeur {$profile->user->name} a été validé avec succès.");
    }

    /**
     * Refuser un groupeur avec motif obligatoire.
     */
    public function rejectProfile(Request $request, GroupeurProfile $profile): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'rejection_reason.required' => 'Le motif du refus est obligatoire.',
            'rejection_reason.min' => 'Le motif doit comporter au moins 5 caractères.',
        ]);

        $profile->update([
            'validation_status' => GroupeurStatus::refuse,
            'rejection_reason' => $request->rejection_reason,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Notification groupeur refusé (Canal BDD Laravel)
        $profile->user->notify(new \App\Notifications\GroupeurRejectedNotification($request->rejection_reason));

        return redirect()->route('admin.groupeurs.show', $profile)
            ->with('warning', "Le dossier de {$profile->user->name} a été refusé avec motif notifié.");
    }
}
