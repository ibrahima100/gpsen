<?php

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Parcours C de bout en bout : inscription groupeur, attente, examen admin, notification et resoumission en cas de refus', function () {
    Storage::fake('local');

    // 1. Inscription du groupeur
    $doc = UploadedFile::fake()->create('cni_initiale.pdf', 1024, 'application/pdf');
    $this->post('/inscription/groupeur', [
        'name' => 'Abdoulaye Diallo',
        'email' => 'abdoulaye@example.com',
        'phone' => '+221 77 111 22 33',
        'bio' => 'Voyageur régulier entre Dakar et Paris depuis 4 ans.',
        'usual_routes' => 'Dakar ⇄ Paris',
        'id_document' => $doc,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('groupeur.waiting'));

    $groupeur = User::where('email', 'abdoulaye@example.com')->first();
    expect($groupeur->isGroupeurValidated())->toBeFalse();

    // 2. Groupeur tente de publier -> bloqué et redirigé vers l'écran d'attente
    $this->actingAs($groupeur)->get('/groupeur/trajets/nouveau')
        ->assertRedirect('/groupeur/attente-validation');

    // 3. Admin ouvre le dossier et refuse pour motif de lisibilité
    $admin = User::factory()->create(['role' => Role::admin]);
    $profile = $groupeur->groupeurProfile;

    $this->actingAs($admin)->post("/admin/groupeurs/{$profile->id}/refuser", [
        'rejection_reason' => 'Pièce floue et date d expiration illisible',
    ])->assertRedirect();

    $profile->refresh();
    expect($profile->validation_status)->toBe(GroupeurStatus::refuse);
    expect($profile->rejection_reason)->toBe('Pièce floue et date d expiration illisible');

    // Vérifier notification envoyée en base
    expect($groupeur->notifications()->count())->toBe(1);

    // 4. Groupeur voit le motif sur son écran et soumet une nouvelle pièce
    $newDoc = UploadedFile::fake()->create('cni_propre.jpg', 1500, 'image/jpeg');
    $this->actingAs($groupeur)->post('/groupeur/nouvelle-soumission', [
        'id_document' => $newDoc,
    ])->assertRedirect();

    $profile->refresh();
    expect($profile->validation_status)->toBe(GroupeurStatus::en_attente);
    expect($profile->rejection_reason)->toBeNull();

    // 5. Admin valide le dossier
    $this->actingAs($admin)->post("/admin/groupeurs/{$profile->id}/valider")
        ->assertRedirect();

    $profile->refresh();
    expect($profile->validation_status)->toBe(GroupeurStatus::valide);
    expect($groupeur->isGroupeurValidated())->toBeTrue();
    expect($groupeur->notifications()->count())->toBe(2);
});

test('Parcours D de bout en bout : publication, consultation, modification et annulation avec motif', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur certifié GPsen',
        'id_document_path' => 'id_documents/fake.pdf',
        'validation_status' => GroupeurStatus::valide,
    ]);

    // 1. Publication d'un trajet
    $this->actingAs($groupeur)->post('/groupeur/trajets', [
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(7)->format('Y-m-d'),
        'arrival_date' => now()->addDays(8)->format('Y-m-d'),
        'capacity_kg' => 40,
        'price_per_kg' => 5000,
        'accepted_items' => 'Plis, bagages fermés, vêtements',
        'prohibited_items' => 'Alcool, denrées périssables fraîches',
        'pickup_info' => 'Dépôt à Almadies la veille',
    ])->assertRedirect(route('groupeur.trips.index'));

    $trip = Trip::where('groupeur_id', $groupeur->id)->first();
    expect($trip)->not->toBeNull();
    expect($trip->status)->toBe(TripStatus::ouvert);
    expect((float) $trip->capacity_kg)->toBe(40.0);

    // 2. Modification du trajet
    $this->actingAs($groupeur)->put("/groupeur/trajets/{$trip->id}", [
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(7)->format('Y-m-d'),
        'capacity_kg' => 35, // ajustement à 35 kg
        'price_per_kg' => 5500,
        'accepted_items' => 'Vêtements seulement',
        'prohibited_items' => 'Tout le reste',
    ])->assertRedirect(route('groupeur.trips.index'));

    $trip->refresh();
    expect((float) $trip->capacity_kg)->toBe(35.0);
    expect($trip->price_per_kg)->toBe(5500);

    // 3. Annulation avec motif obligatoire
    $this->actingAs($groupeur)->post("/groupeur/trajets/{$trip->id}/annuler", [
        'cancelled_reason' => 'Vol annulé par la compagnie aérienne',
    ])->assertRedirect(route('groupeur.trips.index'));

    $trip->refresh();
    expect($trip->status)->toBe(TripStatus::annule);
    expect($trip->cancelled_reason)->toBe('Vol annulé par la compagnie aérienne');
});
