<?php

use App\Enums\GroupeurStatus;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\ShipmentWorkflowService;

/**
 * Couche 4 — Les 5 tests de risque critiques de sécurité et intégrité :
 * 1. Fuite de téléphone / WhatsApp avant acceptation
 * 2. Accès illégitime à la demande ou ressource d'un tiers
 * 3. Surréservation au-delà de la capacité restante
 * 4. Blocage total d'un compte suspendu
 * 5. Rejet strict des transitions de statut interdites
 */
test('Risque 1 : Pas de fuite de téléphone ni de lien WhatsApp avant acceptation formelle', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur, 'phone' => '+221776543210']);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'Voyageur', 'id_document_path' => 'p.pdf']);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 25,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);
    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Colis test fuite',
        'category' => 'vetements',
        'weight_kg' => 3,
        'receiver_name' => 'Modou',
        'receiver_phone' => '+33611223344',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    // L'expéditeur consulte sa demande avant acceptation
    $response = $this->actingAs($expediteur)->get("/expediteur/demandes/{$request->id}");
    $response->assertOk();
    $response->assertSee('Coordonnées protégées');
    $response->assertDontSee('+221776543210');
    $response->assertDontSee('wa.me');
});

test('Risque 2 : Accès interdit à la demande d un autre utilisateur (isolation des données)', function () {
    $expediteur1 = User::factory()->create(['role' => Role::expediteur]);
    $expediteur2 = User::factory()->create(['role' => Role::expediteur]);

    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'G', 'id_document_path' => 'p.pdf']);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(4),
        'capacity_kg' => 20,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur1->id,
        'description' => 'Colis privé de l expéditeur 1',
        'category' => 'documents',
        'weight_kg' => 1,
        'receiver_name' => 'Awa',
        'receiver_phone' => '+33600000000',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    // L'expéditeur 2 tente d'accéder au colis de l'expéditeur 1
    $response = $this->actingAs($expediteur2)->get("/expediteur/demandes/{$request->id}");
    $response->assertStatus(403);
});

test('Risque 3 : Impossible de réserver au-delà de la capacité restante du trajet', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'G', 'id_document_path' => 'p.pdf']);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 10, // 10 kg max
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);

    // Tentative d'envoi de 12 kg (dépassement)
    $response = $this->actingAs($expediteur)->post("/expediteur/trajets/{$trip->id}/demande", [
        'description' => 'Colis trop lourd pour le trajet',
        'category' => 'vetements',
        'weight_kg' => 12,
        'receiver_name' => 'Jean',
        'receiver_phone' => '+33612345678',
        'receiver_city' => 'Paris',
    ]);

    $response->assertSessionHasErrors(['weight_kg']);
    expect(ShipmentRequest::where('trip_id', $trip->id)->count())->toBe(0);
});

test('Risque 4 : Un compte suspendu est immédiatement déconnecté et bloqué sur toute l application', function () {
    $user = User::factory()->create([
        'role' => Role::expediteur,
        'status' => 'suspendu',
        'password' => 'secret123',
    ]);

    // 1. Connexion refusée
    $this->post('/connexion', [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertSessionHasErrors('email');

    // 2. Requête authentifiée bloquée par le middleware EnsureUserNotSuspended
    $response = $this->actingAs($user)->get('/expediteur/tableau-de-bord');
    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('Risque 5 : Rejet strict des transitions de statut de colis interdites', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
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
    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Colis test statut',
        'category' => 'vetements',
        'weight_kg' => 2,
        'receiver_name' => 'Aminata',
        'receiver_phone' => '123456',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    $workflow = app(ShipmentWorkflowService::class);

    // Impossible de passer directement de 'en_attente' à 'livree'
    expect(fn () => $workflow->transition($request, 'livree', $groupeur))
        ->toThrow(InvalidArgumentException::class);

    // Impossible de passer de 'en_attente' à 'en_transit'
    expect(fn () => $workflow->transition($request, 'en_transit', $groupeur))
        ->toThrow(InvalidArgumentException::class);

    // Le statut n'a pas bougé
    expect($request->refresh()->status)->toBe(RequestStatus::en_attente);
});
