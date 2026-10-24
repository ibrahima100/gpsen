<?php

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('la page de choix d inscription s affiche correctement', function () {
    $response = $this->get('/inscription');
    $response->assertStatus(200);
    $response->assertSee('Je suis Expéditeur');
    $response->assertSee('Je suis Groupeur');
});

test('un expediteur peut s inscrire avec 4 champs', function () {
    $response = $this->post('/inscription/expediteur', [
        'name' => 'Awa Diop',
        'email' => 'awa@example.com',
        'phone' => '+221 77 123 45 67',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $user = User::where('email', 'awa@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe(Role::expediteur);
    expect($user->phone)->toBe('+221 77 123 45 67');
    expect($user->status)->toBe('actif');

    $response->assertRedirect('/dashboard');
});

test('un groupeur peut s inscrire et son dossier passe au statut en_attente', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->create('cni.pdf', 1024, 'application/pdf');

    $response = $this->post('/inscription/groupeur', [
        'name' => 'Moussa Sow',
        'email' => 'moussa@example.com',
        'phone' => '+221 70 987 65 43',
        'bio' => 'Voyageur régulier entre Dakar et Paris depuis 3 ans.',
        'usual_routes' => 'Dakar - Paris',
        'id_document' => $file,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $user = User::where('email', 'moussa@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe(Role::groupeur);
    expect($user->groupeurProfile)->not->toBeNull();
    expect($user->groupeurProfile->validation_status)->toBe(GroupeurStatus::en_attente);

    // Vérifier stockage privé
    Storage::disk('local')->assertExists($user->groupeurProfile->id_document_path);

    $response->assertRedirect(route('groupeur.waiting'));
});

test('un groupeur non valide est redirige vers la page d attente', function () {
    $user = User::factory()->create([
        'role' => Role::groupeur,
    ]);

    $user->groupeurProfile()->create([
        'bio' => 'Bio de test pour le groupeur.',
        'id_document_path' => 'id_documents/test.pdf',
        'validation_status' => GroupeurStatus::en_attente,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertRedirect(route('groupeur.waiting'));
});

test('un utilisateur suspendu ne peut pas se connecter', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'status' => 'suspendu',
        'suspension_reason' => 'Non respect des règles de fret',
    ]);

    $response = $this->post('/connexion', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $this->assertGuest();
});

test('isolation des roles : un expediteur ne peut pas acceder a l espace admin', function () {
    $user = User::factory()->create([
        'role' => Role::expediteur,
    ]);

    $response = $this->actingAs($user)->get('/admin/tableau-de-bord');
    $response->assertStatus(403);
});
