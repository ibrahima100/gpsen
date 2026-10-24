<?php

use App\Enums\GroupeurStatus;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;

test('Parcours A -> E complet : Visiteur decouvre, recherche, ouvre un trajet, se connecte/s inscrit, et cree sa demande d envoi', function () {
    // 1. Un groupeur validé a publié un trajet ouvert
    $groupeur = User::factory()->create([
        'name' => 'Amadou Groupeur Pro',
        'role' => Role::groupeur,
    ]);
    $groupeur->groupeurProfile()->create([
        'bio' => 'Voyageur régulier Dakar-Paris.',
        'validation_status' => GroupeurStatus::valide,
        'id_document_path' => 'id_documents/test.pdf',
    ]);

    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'capacity_kg' => 30,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
        'pickup_info' => 'Dépôt à Almadies la veille du départ',
    ]);

    // 2. Le visiteur (non connecté) arrive sur la page d'accueil
    $homeResponse = $this->get('/');
    $homeResponse->assertStatus(200);
    $homeResponse->assertSee('Dakar');
    $homeResponse->assertSee('Paris');

    // 3. Il utilise la recherche par destination
    $searchResponse = $this->get('/trajets?destination=Paris');
    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('Dakar');
    $searchResponse->assertSee('Paris');

    // 4. Il ouvre la page détail du trajet
    $detailResponse = $this->get("/trajets/{$trip->id}");
    $detailResponse->assertStatus(200);
    $detailResponse->assertSee('Amadou Groupeur Pro');
    $detailResponse->assertSee('Dépôt à Almadies');
    // Le contact WhatsApp reste masqué pour le visiteur
    $detailResponse->assertDontSee('+221');
    $detailResponse->assertSee('Se connecter pour envoyer une demande');

    // 5. Il s'inscrit en tant qu'expéditeur
    $registerResponse = $this->post('/inscription/expediteur', [
        'name' => 'Fatou Kiné',
        'email' => 'fatou.kine@example.com',
        'phone' => '+221 77 555 44 33',
        'password' => 'secret1234',
        'password_confirmation' => 'secret1234',
    ]);
    $this->assertAuthenticated();

    $expediteur = User::where('email', 'fatou.kine@example.com')->first();
    expect($expediteur->role)->toBe(Role::expediteur);

    // 6. Parcours E : Il remplit et soumet sa demande d'envoi pour ce trajet
    $formResponse = $this->actingAs($expediteur)->get("/expediteur/trajets/{$trip->id}/demande");
    $formResponse->assertStatus(200);
    $formResponse->assertSee('Le colis');
    $formResponse->assertSee('Le destinataire');

    $demandeResponse = $this->actingAs($expediteur)->post("/expediteur/trajets/{$trip->id}/demande", [
        'description' => 'Deux colis de vêtements traditionnels et chaussures emballées',
        'category' => 'vetements',
        'weight_kg' => 4.5,
        'declared_value' => 35000,
        'receiver_name' => 'Samba Diallo',
        'receiver_phone' => '+33 6 99 88 77 66',
        'receiver_city' => 'Paris',
    ]);

    // Redirigé vers sa liste de demandes avec message de succès
    $demandeResponse->assertRedirect(route('expediteur.demandes.index'));
    $demandeResponse->assertSessionHas('success');

    // 7. Vérifications en base : demande créée en attente + notification groupeur
    $demande = ShipmentRequest::where('trip_id', $trip->id)->first();
    expect($demande)->not->toBeNull();
    expect($demande->sender_id)->toBe($expediteur->id);
    expect((float) $demande->weight_kg)->toBe(4.5);
    expect($demande->status)->toBe(RequestStatus::en_attente);
    expect($demande->receiver_name)->toBe('Samba Diallo');

    // Notification reçue par le groupeur
    expect($groupeur->notifications()->count())->toBe(1);
    $notification = $groupeur->notifications()->first();
    expect($notification->data['message'])->toContain('Fatou Kiné');
    expect($notification->data['message'])->toContain('4.5');
});
