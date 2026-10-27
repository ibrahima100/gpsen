<?php

use App\Enums\GroupeurStatus;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\ShipmentWorkflowService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * TRD §7 — Les 9 tests critiques
 */

// 1. Inscription expéditeur et inscription groupeur (compte "en attente")
test('TRD-1 : Inscription expediteur et groupeur (compte en attente)', function () {
    Storage::fake('local');

    // Inscription expéditeur
    $this->post('/inscription/expediteur', [
        'name' => 'Aissatou Diallo',
        'email' => 'aissatou@example.com',
        'phone' => '+221 77 100 20 30',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])->assertRedirect(route('dashboard'));

    $expediteur = User::where('email', 'aissatou@example.com')->first();
    expect($expediteur->role)->toBe(Role::expediteur);
    expect($expediteur->status)->toBe('actif');

    \Illuminate\Support\Facades\Auth::logout();

    // Inscription groupeur avec pièce d'identité
    $file = UploadedFile::fake()->create('cni.pdf', 1024, 'application/pdf');
    $this->post('/inscription/groupeur', [
        'name' => 'Babacar Diagne',
        'email' => 'babacar@example.com',
        'phone' => '+221 77 400 50 60',
        'bio' => 'Voyageur régulier Dakar - Paris depuis 5 ans.',
        'id_document' => $file,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])->assertRedirect(route('groupeur.waiting'));

    $groupeur = User::where('email', 'babacar@example.com')->first();
    expect($groupeur->role)->toBe(Role::groupeur);
    expect($groupeur->groupeurProfile->validation_status)->toBe(GroupeurStatus::en_attente);
});

// 2. Un groupeur non validé ne peut pas publier ; un groupeur validé le peut
test('TRD-2 : Groupeur non valide bloque, groupeur valide peut publier', function () {
    $groupeurAttente = User::factory()->create(['role' => Role::groupeur]);
    $groupeurAttente->groupeurProfile()->create(['bio' => 'bio', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::en_attente]);

    // Tentative par groupeur non validé -> bloqué
    $this->actingAs($groupeurAttente)->get('/groupeur/trajets/nouveau')
        ->assertRedirect('/groupeur/attente-validation');

    $groupeurValide = User::factory()->create(['role' => Role::groupeur]);
    $groupeurValide->groupeurProfile()->create(['bio' => 'bio', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    // Groupeur validé -> autorisé
    $this->actingAs($groupeurValide)->post('/groupeur/trajets', [
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'capacity_kg' => 25,
        'price_per_kg' => 5000,
    ])->assertRedirect(route('groupeur.trips.index'));

    expect(Trip::where('groupeur_id', $groupeurValide->id)->count())->toBe(1);
});

// 3. L'admin valide un groupeur et celui-ci est notifié
test('TRD-3 : Admin valide un groupeur et celui-ci est notifie', function () {
    $admin = User::factory()->create(['role' => Role::admin]);
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $profile = $groupeur->groupeurProfile()->create(['bio' => 'bio', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::en_attente]);

    $this->actingAs($admin)->post("/admin/groupeurs/{$profile->id}/valider")->assertRedirect();

    $profile->refresh();
    expect($profile->validation_status)->toBe(GroupeurStatus::valide);
    expect($groupeur->notifications()->count())->toBe(1);
});

// 4. Une demande d'envoi ne peut pas dépasser la capacité restante
test('TRD-4 : Demande ne peut pas depasser la capacite restante', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['bio' => 'bio', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 10,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);

    $response = $this->actingAs($expediteur)->post("/expediteur/trajets/{$trip->id}/demande", [
        'description' => 'Carton trop lourd de 15 kg',
        'category' => 'vetements',
        'weight_kg' => 15,
        'receiver_name' => 'Destinataire',
        'receiver_phone' => '+33 6 00 00 00 00',
        'receiver_city' => 'Paris',
    ]);

    $response->assertSessionHasErrors('weight_kg');
    expect(ShipmentRequest::where('trip_id', $trip->id)->count())->toBe(0);
});

// 5. L'acceptation met à jour la capacité ; le trajet passe à "complet" à zéro
test('TRD-5 : L acceptation met a jour la capacite et passe le trajet a complet a zero', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['bio' => 'bio', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 8,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);
    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Colis de 8 kg',
        'category' => 'vetements',
        'weight_kg' => 8,
        'receiver_name' => 'Destinataire',
        'receiver_phone' => '123',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    $this->actingAs($groupeur)->post("/groupeur/demandes/{$request->id}/accepter")->assertRedirect();

    $trip->refresh();
    expect($trip->status)->toBe(TripStatus::complet);
});

// 6. Les transitions de statut interdites sont rejetées
test('TRD-6 : Transitions de statut interdites rejetees', function () {
    $service = app(ShipmentWorkflowService::class);
    $user = User::factory()->create(['role' => Role::groupeur]);
    $trip = Trip::create([
        'groupeur_id' => $user->id,
        'origin_country' => 'SN',
        'origin_city' => 'Dakar',
        'destination_country' => 'FR',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 10,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $req = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $user->id,
        'description' => 'Colis',
        'category' => 'vetements',
        'weight_kg' => 2,
        'receiver_name' => 'Dest',
        'receiver_phone' => '123',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    // Interdit : de en_attente à livree directement
    expect(fn () => $service->transition($req, 'livree', $user))->toThrow(\InvalidArgumentException::class);
});

// 7. Un utilisateur ne peut pas modifier le trajet ou la demande d'un autre
test('TRD-7 : Isolation des ressources entre utilisateurs', function () {
    $groupeur1 = User::factory()->create(['role' => Role::groupeur]);
    $groupeur1->groupeurProfile()->create(['bio' => 'G1', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $groupeur2 = User::factory()->create(['role' => Role::groupeur]);
    $groupeur2->groupeurProfile()->create(['bio' => 'G2', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $trip1 = Trip::create([
        'groupeur_id' => $groupeur1->id,
        'origin_country' => 'SN',
        'origin_city' => 'Dakar',
        'destination_country' => 'FR',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 20,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    // Groupeur 2 tente de modifier le trajet de Groupeur 1 -> 403 Forbidden
    $this->actingAs($groupeur2)->put("/groupeur/trajets/{$trip1->id}", [
        'origin_country' => 'SN',
        'origin_city' => 'Dakar',
        'destination_country' => 'FR',
        'destination_city' => 'Lyon',
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'capacity_kg' => 15,
        'price_per_kg' => 4500,
    ])->assertStatus(403);
});

// 8. Le lien WhatsApp n'est pas visible avant acceptation
test('TRD-8 : Le lien WhatsApp n est pas visible avant acceptation', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur, 'phone' => '+221 77 999 88 77']);
    $groupeur->groupeurProfile()->create(['bio' => 'G', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'SN',
        'origin_city' => 'Dakar',
        'destination_country' => 'FR',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 20,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);
    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Colis de test',
        'category' => 'vetements',
        'weight_kg' => 2,
        'receiver_name' => 'Dest',
        'receiver_phone' => '123',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente, // En attente
    ]);

    // Visite avant acceptation -> WhatsApp masqué
    $this->actingAs($expediteur)->get("/expediteur/demandes/{$request->id}")
        ->assertStatus(200)
        ->assertSee('Coordonnées protégées')
        ->assertDontSee('wa.me');

    // Passage à acceptee -> WhatsApp débloqué
    $request->update(['status' => RequestStatus::acceptee]);

    $this->actingAs($expediteur)->get("/expediteur/demandes/{$request->id}")
        ->assertStatus(200)
        ->assertSee('Contacter sur WhatsApp')
        ->assertSee('wa.me');
});

// 9. Un avis n'est possible que sur une demande livrée, et une seule fois par partie
test('TRD-9 : Un avis n est possible que sur une demande livree et une seule fois', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur, 'phone' => '+221 77 111 22 33']);
    $profile = $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur pro',
        'id_document_path' => 'p.pdf',
        'validation_status' => GroupeurStatus::valide,
        'average_rating' => 0.0,
        'ratings_count' => 0,
    ]);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'SN',
        'origin_city' => 'Dakar',
        'destination_country' => 'FR',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(3),
        'capacity_kg' => 20,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);
    $stranger = User::factory()->create(['role' => Role::expediteur]);

    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Colis test avis',
        'category' => 'vetements',
        'weight_kg' => 3,
        'receiver_name' => 'Jean',
        'receiver_phone' => '0601020304',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_transit, // Non livrée !
    ]);

    // 1. Rejet si la demande n'est pas livrée
    $this->actingAs($expediteur)->post("/demandes/{$request->id}/avis", [
        'rating' => 5,
        'comment' => 'Super voyageur !',
    ])->assertStatus(422);

    // 2. Passage à livrée
    $request->update(['status' => RequestStatus::livree]);

    // 3. Rejet si utilisateur non partie prenante
    $this->actingAs($stranger)->post("/demandes/{$request->id}/avis", [
        'rating' => 4,
        'comment' => 'Je ne suis pas le client...',
    ])->assertStatus(403);

    // 4. Succès pour l'expéditeur
    $this->actingAs($expediteur)->post("/demandes/{$request->id}/avis", [
        'rating' => 5,
        'comment' => 'Livraison rapide et soignée, merci !',
    ])->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'shipment_request_id' => $request->id,
        'author_id' => $expediteur->id,
        'target_id' => $groupeur->id,
        'rating' => 5,
    ]);

    // Vérifier recalcul note groupeur
    $profile->refresh();
    expect((float) $profile->average_rating)->toBe(5.0);
    expect($profile->ratings_count)->toBe(1);

    // 5. Rejet d'un deuxième avis par le même expéditeur sur la même livraison (unicité)
    $this->actingAs($expediteur)->post("/demandes/{$request->id}/avis", [
        'rating' => 4,
        'comment' => 'Deuxième tentative non autorisée',
    ])->assertStatus(422);
});
