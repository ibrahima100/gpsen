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
        $warnings = \App\Models\Warning::with('admin')
            ->where('groupeur_id', $profile->user_id)
            ->latest('sent_at')
            ->get();

        return view('admin.groupeurs.show', compact('profile', 'warnings'));
    }

    /**
     * Visualisation sécurisée de la pièce d'identité (stockage privé avec audit).
     */
    public function viewDocument(Request $request, GroupeurProfile $profile): StreamedResponse
    {
        if (! Storage::disk('local')->exists($profile->id_document_path)) {
            abort(404, 'Document introuvable sur le disque privé.');
        }

        // Journalisation de sécurité de l'accès au document
        \App\Models\DocumentAccessLog::create([
            'admin_id' => auth()->id(),
            'groupeur_profile_id' => $profile->id,
            'document_path' => $profile->id_document_path,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return Storage::disk('local')->response(
            $profile->id_document_path,
            'piece_identite_'.$profile->user_id.'.'.pathinfo($profile->id_document_path, PATHINFO_EXTENSION),
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
        $profile->user->notify(new \App\Notifications\GroupeurValidatedNotification);

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

    /**
     * Envoyer un avertissement officiel au groupeur avant fermeture potentielle de compte.
     */
    public function warnProfile(Request $request, GroupeurProfile $profile): RedirectResponse
    {
        $validated = $request->validate([
            'reason_preset' => ['required', 'string'],
            'custom_reason' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:1000'],
            'delay_days' => ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'reason_preset.required' => 'Le motif de l\'avertissement est obligatoire.',
            'delay_days.required' => 'Le délai de régularisation est obligatoire.',
            'delay_days.min' => 'Le délai minimum est de 1 jour.',
            'delay_days.max' => 'Le délai maximum est de 30 jours.',
        ]);

        $finalReason = ($validated['reason_preset'] === 'autre' && ! empty($validated['custom_reason']))
            ? $validated['custom_reason']
            : $validated['reason_preset'];

        $warning = \App\Models\Warning::create([
            'groupeur_id' => $profile->user_id,
            'admin_id' => auth()->id(),
            'reason' => $finalReason,
            'details' => $validated['details'] ?? null,
            'delay_days' => (int) $validated['delay_days'],
            'sent_at' => now(),
        ]);

        // Envoi du courriel avec le motif, le délai et la marche à suivre
        if ($profile->user && $profile->user->email) {
            \Illuminate\Support\Facades\Mail::to($profile->user->email)->send(
                new \App\Mail\WarningBeforeAccountClosureMail($warning)
            );
        }

        // Notification dans la plateforme
        $profile->user->notify(new \App\Notifications\WarningReceivedNotification($warning));

        return redirect()->route('admin.groupeurs.show', $profile)
            ->with('success', "Avertissement officiel envoyé avec succès par courriel à {$profile->user->name}. Délai accordé : {$warning->delay_days} jours.");
    }
}
