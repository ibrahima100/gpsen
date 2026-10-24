<?php

use App\Enums\GroupeurStatus;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;

test('la page d accueil affiche le hero, le formulaire de recherche et les trajets recents', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'Groupeur', 'id_document_path' => 'doc.pdf']);

    Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 20,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Envoyez vos colis moins cher');
    $response->assertSee('Trouver un trajet');
    $response->assertSee('Dakar');
    $response->assertSee('Paris');
});

test('la page liste des trajets permet de filtrer par destination', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'Groupeur', 'id_document_path' => 'doc.pdf']);

    Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Bordeaux',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 20,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'Italie',
        'destination_city' => 'Milan',
        'departure_date' => now()->addDays(6),
        'capacity_kg' => 15,
        'price_per_kg' => 4500,
        'status' => TripStatus::ouvert,
    ]);

    $response = $this->get('/trajets?destination=Milan');
    $response->assertStatus(200);
    $response->assertSee('Milan');
    $response->assertDontSee('Bordeaux');
});

test('detail du trajet affiche le profil groupeur et les conditions', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur, 'name' => 'Fatou Transport']);
    $groupeur->groupeurProfile()->create([
        'validation_status' => GroupeurStatus::valide,
        'bio' => 'Spécialiste fret Dakar-Paris',
        'id_document_path' => 'doc.pdf',
    ]);

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
        'pickup_info' => 'Dépôt à Yoff',
    ]);

    $response = $this->get("/trajets/{$trip->id}");
    $response->assertStatus(200);
    $response->assertSee('Fatou Transport');
    $response->assertSee('Dépôt à Yoff');
    $response->assertSee('25 kg');
});

test('expediteur peut envoyer une demande d envoi sur un trajet avec capacite suffisante', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'G', 'id_document_path' => 'd.pdf']);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 30,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);

    $response = $this->actingAs($expediteur)->post("/expediteur/trajets/{$trip->id}/demande", [
        'description' => 'Deux colis de vêtements traditionnels et chaussures',
        'category' => 'vetements',
        'weight_kg' => 5,
        'declared_value' => 40000,
        'receiver_name' => 'Mamadou Ba',
        'receiver_phone' => '+33 6 11 22 33 44',
        'receiver_city' => 'Paris',
    ]);

    $response->assertRedirect('/expediteur/tableau-de-bord');

    $demande = ShipmentRequest::where('trip_id', $trip->id)->first();
    expect($demande)->not->toBeNull();
    expect($demande->sender_id)->toBe($expediteur->id);
    expect((float) $demande->weight_kg)->toBe(5.0);
    expect($demande->status)->toBe(RequestStatus::en_attente);

    // Vérifier notification au groupeur
    expect($groupeur->notifications()->count())->toBe(1);
});

test('une demande d envoi ne peut pas depasser la capacite restante du trajet', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create(['validation_status' => GroupeurStatus::valide, 'bio' => 'G', 'id_document_path' => 'd.pdf']);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 10, // 10 kg maxi
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $expediteur = User::factory()->create(['role' => Role::expediteur]);

    // Tentative d'envoyer 15 kg
    $response = $this->actingAs($expediteur)->post("/expediteur/trajets/{$trip->id}/demande", [
        'description' => 'Gros carton trop lourd',
        'category' => 'autre',
        'weight_kg' => 15,
        'receiver_name' => 'Mamadou Ba',
        'receiver_phone' => '+33 6 11 22 33 44',
        'receiver_city' => 'Paris',
    ]);

    $response->assertSessionHasErrors('weight_kg');
    expect(ShipmentRequest::where('trip_id', $trip->id)->count())->toBe(0);
});
