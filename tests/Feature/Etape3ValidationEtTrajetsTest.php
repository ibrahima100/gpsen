<?php

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use App\Services\TripCapacityService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin peut lister les groupeurs et voir les dossiers', function () {
    $admin = User::factory()->create(['role' => Role::admin]);

    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur pro depuis 3 ans',
        'id_document_path' => 'id_documents/test.pdf',
        'validation_status' => GroupeurStatus::en_attente,
    ]);

    $response = $this->actingAs($admin)->get('/admin/groupeurs');
    $response->assertStatus(200);
    $response->assertSee($groupeur->name);
});

test('admin peut valider un dossier groupeur', function () {
    $admin = User::factory()->create(['role' => Role::admin]);
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $profile = $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur régulier',
        'id_document_path' => 'id_documents/test.pdf',
        'validation_status' => GroupeurStatus::en_attente,
    ]);

    $response = $this->actingAs($admin)->post("/admin/groupeurs/{$profile->id}/valider");
    $response->assertRedirect();

    $profile->refresh();
    expect($profile->validation_status)->toBe(GroupeurStatus::valide);
    expect($profile->validated_by)->toBe($admin->id);
    expect($profile->validated_at)->not->toBeNull();
});

test('admin peut refuser un dossier groupeur avec un motif', function () {
    $admin = User::factory()->create(['role' => Role::admin]);
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $profile = $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur régulier',
        'id_document_path' => 'id_documents/test.pdf',
        'validation_status' => GroupeurStatus::en_attente,
    ]);

    $response = $this->actingAs($admin)->post("/admin/groupeurs/{$profile->id}/refuser", [
        'rejection_reason' => 'Pièce floue et illisible',
    ]);
    $response->assertRedirect();

    $profile->refresh();
    expect($profile->validation_status)->toBe(GroupeurStatus::refuse);
    expect($profile->rejection_reason)->toBe('Pièce floue et illisible');
});

test('groupeur non valide ne peut pas publier de trajet', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur non validé',
        'id_document_path' => 'id_documents/test.pdf',
        'validation_status' => GroupeurStatus::en_attente,
    ]);

    $response = $this->actingAs($groupeur)->get('/groupeur/trajets/nouveau');
    $response->assertRedirect('/groupeur/attente-validation');
});

test('groupeur valide peut publier un trajet', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $groupeur->groupeurProfile()->create([
        'bio' => 'Groupeur certifié',
        'id_document_path' => 'id_documents/test.pdf',
        'validation_status' => GroupeurStatus::valide,
    ]);

    $response = $this->actingAs($groupeur)->post('/groupeur/trajets', [
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'capacity_kg' => 30,
        'price_per_kg' => 5000,
        'accepted_items' => 'Vêtements, chaussures',
        'prohibited_items' => 'Liquides, médicaments',
    ]);

    $response->assertRedirect(route('groupeur.trips.index'));

    $trip = Trip::where('groupeur_id', $groupeur->id)->first();
    expect($trip)->not->toBeNull();
    expect($trip->origin_city)->toBe('Dakar');
    expect($trip->destination_city)->toBe('Paris');
    expect((float) $trip->capacity_kg)->toBe(30.0);
    expect($trip->status)->toBe(TripStatus::ouvert);
});

test('isolation : un groupeur ne peut pas modifier le trajet d un autre groupeur', function () {
    $groupeur1 = User::factory()->create(['role' => Role::groupeur]);
    $groupeur1->groupeurProfile()->create(['bio' => 'G1', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $groupeur2 = User::factory()->create(['role' => Role::groupeur]);
    $groupeur2->groupeurProfile()->create(['bio' => 'G2', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);

    $trip1 = Trip::create([
        'groupeur_id' => $groupeur1->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 20,
        'price_per_kg' => 4500,
        'status' => TripStatus::ouvert,
    ]);

    // Tentative de modification par groupeur2
    $response = $this->actingAs($groupeur2)->put("/groupeur/trajets/{$trip1->id}", [
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Marseille',
        'departure_date' => now()->addDays(6)->format('Y-m-d'),
        'capacity_kg' => 15,
        'price_per_kg' => 4000,
    ]);

    $response->assertStatus(403);
});

test('service de capacite calcule correctement la disponibilite', function () {
    $groupeur = User::factory()->create(['role' => Role::groupeur]);
    $trip = Trip::create([
        'groupeur_id' => $groupeur->id,
        'origin_country' => 'Sénégal',
        'origin_city' => 'Dakar',
        'destination_country' => 'France',
        'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5),
        'capacity_kg' => 50,
        'price_per_kg' => 5000,
        'status' => TripStatus::ouvert,
    ]);

    $service = new TripCapacityService();
    expect($service->getRemainingCapacity($trip))->toBe(50.0);
    expect($service->canAccommodate($trip, 30.0))->toBeTrue();
    expect($service->canAccommodate($trip, 55.0))->toBeFalse();
});
