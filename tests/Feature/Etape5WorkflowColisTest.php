<?php

use App\Enums\GroupeurStatus;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\ShipmentWorkflowService;

test('Parcours F complet : Groupeur traite une demande (accepte, passage a complet a 0kg, WhatsApp active, collectee, en_transit, livree)', function () {
    $groupeur = User::factory()->create(['name' => 'Modou Ndiaye', 'phone' => '+221 77 000 11 22', 'role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'Voyageur', 'id_document_path' => 'doc.pdf']);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 10, // 10 kg
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['name' => 'Awa Fall', 'phone' => '+221 77 333 44 55', 'role' => Role::expediteur]);

    $request = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Colis de vêtements de 10 kg',
        'category' => 'vetements',
        'weight_kg' => 10, // prend la totalité de la capacité
        'receiver_name' => 'Cheikh Fall',
        'receiver_phone' => '+33 6 12 34 56 78',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    // 1. Avant acceptation : le lien WhatsApp n'est pas actif pour l'expéditeur
    $this->actingAs($expediteur)->get("/expediteur/demandes/{$request->id}")
        ->assertStatus(200)
        ->assertSee('Coordonnées protégées')
        ->assertDontSee('wa.me');

    // 2. Le groupeur accepte la demande -> capacité tombe à 0 et trajet passe à 'complet'
    $this->actingAs($groupeur)->post("/groupeur/demandes/{$request->id}/accepter")
        ->assertRedirect();

    $request->refresh();
    $trip->refresh();

    expect($request->status)->toBe(RequestStatus::acceptee);
    expect($trip->status)->toBe(TripStatus::complet); // Passage automatique à complet
    expect($expediteur->notifications()->count())->toBe(1); // Notifié de l'acceptation

    // 3. Après acceptation : le lien WhatsApp est actif des 2 côtés
    $this->actingAs($expediteur)->get("/expediteur/demandes/{$request->id}")
        ->assertStatus(200)
        ->assertSee('Contacter sur WhatsApp')
        ->assertSee('wa.me');

    $this->actingAs($groupeur)->get("/groupeur/demandes/{$request->id}")
        ->assertStatus(200)
        ->assertSee('Échanger sur WhatsApp')
        ->assertSee('wa.me');

    // 4. Groupeur passe à 'collectee'
    $this->actingAs($groupeur)->post("/groupeur/demandes/{$request->id}/statut", [
        'status' => 'collectee',
        'note' => 'Colis récupéré au point de dépôt',
    ])->assertRedirect();

    $request->refresh();
    expect($request->status)->toBe(RequestStatus::collectee);

    // 5. Groupeur passe à 'en_transit'
    $this->actingAs($groupeur)->post("/groupeur/demandes/{$request->id}/statut", [
        'status' => 'en_transit',
        'note' => 'Départ en avion confirmé',
    ])->assertRedirect();

    $request->refresh();
    expect($request->status)->toBe(RequestStatus::en_transit);

    // 6. Groupeur passe à 'livree'
    $this->actingAs($groupeur)->post("/groupeur/demandes/{$request->id}/statut", [
        'status' => 'livree',
        'note' => 'Remis en mains propres à Cheikh Fall',
    ])->assertRedirect();

    $request->refresh();
    expect($request->status)->toBe(RequestStatus::livree);
    expect($request->delivered_at)->not->toBeNull();
    expect($groupeur->groupeurProfile->fresh()->delivered_count)->toBe(1);
});

test('transitions de statut interdites sont fermement rejetees', function () {
    $service = app(ShipmentWorkflowService::class);

    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $expediteur = User::factory()->create(['role' => Role::expediteur]);
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

    $req = ShipmentRequest::create([
        'trip_id' => $trip->id,
        'sender_id' => $expediteur->id,
        'description' => 'Test colis',
        'category' => 'autre',
        'weight_kg' => 2,
        'receiver_name' => 'Dest',
        'receiver_phone' => '123',
        'receiver_city' => 'Paris',
        'status' => RequestStatus::en_attente,
    ]);

    // Passer de 'en_attente' directement à 'livree' est strictement interdit
    expect(fn () => $service->transition($req, 'livree', $groupeur))->toThrow(InvalidArgumentException::class);

    // Passer de 'refusee' à 'acceptee' est interdit
    $req->status = RequestStatus::refusee;
    $req->save();
    expect(fn () => $service->transition($req, 'acceptee', $groupeur))->toThrow(InvalidArgumentException::class);
});
