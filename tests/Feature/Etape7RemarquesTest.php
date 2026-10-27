<?php

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use App\Mail\AccountWarningMail;
use App\Mail\ExpediteurWelcomeMail;
use App\Mail\GroupeurRegisteredMail;
use App\Mail\GroupeurRejectedMail;
use App\Mail\GroupeurValidatedMail;
use App\Mail\ReportReceivedMail;
use App\Models\Report;
use App\Models\Trip;
use App\Models\User;
use App\Support\Geo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Recette utilisateur — remarques n°1 à 23 (tests écrits avant l'implémentation).
 */

function admin7(): User
{
    return User::factory()->create(['role' => Role::admin, 'status' => 'actif']);
}

function groupeur7(GroupeurStatus $status = GroupeurStatus::valide): User
{
    $g = User::factory()->create(['role' => Role::groupeur, 'status' => 'actif']);
    $g->groupeurProfile()->create(['bio' => 'Voyageur', 'id_document_path' => 'id_documents/t.pdf', 'validation_status' => $status]);

    return $g;
}

// ───────────── Côté admin : 1, 2, 3, 5, 18 ─────────────

test('R1-R2 : admin sans lien Mon profil, avec menu avatar et "Gestion des utilisateurs"', function () {
    $this->actingAs(admin7())->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee('Mon profil')
        ->assertSee('Gestion des utilisateurs')
        ->assertSee(route('profile.edit'))
        ->assertSee('Changer le mot de passe');
});

test('R3-R18 : la cloche affiche les notifications directement dans le menu', function () {
    $admin = admin7();
    $admin->notifications()->create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Nouveau dossier à valider', 'message' => 'Un groupeur attend.', 'action_url' => url('/admin/groupeurs')],
    ]);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertSee('Nouveau dossier à valider')
        ->assertSee('Un groupeur attend.');

    $exp = User::factory()->create(['role' => Role::expediteur]);
    $exp->notifications()->create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Colis accepté', 'message' => 'Votre colis est accepté.', 'action_url' => url('/')],
    ]);
    $this->actingAs($exp)->get(route('expediteur.dashboard'))->assertSee('Colis accepté');
});

test('R5-R21 : photo de profil pour tous les rôles et téléphone modifiable', function () {
    Storage::fake('public');
    foreach ([Role::admin, Role::expediteur, Role::groupeur] as $role) {
        $user = User::factory()->create(['role' => $role, 'status' => 'actif']);
        if ($role === Role::groupeur) {
            $user->groupeurProfile()->create(['bio' => 'x', 'id_document_path' => 'p.pdf', 'validation_status' => GroupeurStatus::valide]);
        }

        $this->actingAs($user)->get(route('profile.edit'))->assertOk()->assertSee('Photo de profil');

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Nom Modifié',
            'email' => $user->email,
            'phone' => '+221 77 000 11 22',
            'avatar' => UploadedFile::fake()->image('moi.jpg', 200, 200),
        ])->assertRedirect(route('profile.edit'));

        $user->refresh();
        expect($user->avatar_path)->not->toBeNull();
        expect($user->phone)->toBe('+221 77 000 11 22');
        Storage::disk('public')->assertExists($user->avatar_path);
    }
});

test('R5 : un fichier non image est refusé comme photo de profil', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => Role::expediteur]);
    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name, 'email' => $user->email,
        'avatar' => UploadedFile::fake()->create('virus.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('avatar');
});

// ───────────── Couleurs de statuts : 4 ─────────────

test('R4 : refusé rouge, en attente jaune, validé vert', function () {
    $rendu = fn (GroupeurStatus $s) => Blade::render('<x-status-badge :status="$s" />', ['s' => $s]);

    expect($rendu(GroupeurStatus::refuse))->toContain('bg-red-100');
    expect($rendu(GroupeurStatus::en_attente))->toContain('bg-yellow-100');
    expect($rendu(GroupeurStatus::valide))->toContain('bg-green-100');

    $this->actingAs(admin7());
    groupeur7(GroupeurStatus::refuse);
    $this->get(route('admin.groupeurs.index'))->assertOk();
});

// ───────────── E-mails : 6 à 10 ─────────────

test('R8 : e-mail de bienvenue à l expéditeur à l inscription', function () {
    Mail::fake();
    $this->post('/inscription/expediteur', [
        'name' => 'Aissatou Diallo', 'email' => 'aissatou@example.com', 'phone' => '+221 77 100 20 30',
        'password' => 'secret123', 'password_confirmation' => 'secret123',
    ]);
    Mail::assertSent(ExpediteurWelcomeMail::class, fn ($m) => $m->hasTo('aissatou@example.com'));
});

test('R7 : e-mail "compte en cours d examen, réponse sous 48 heures" au groupeur inscrit', function () {
    Mail::fake();
    Storage::fake('local');
    $this->post('/inscription/groupeur', [
        'name' => 'Babacar Diagne', 'email' => 'babacar@example.com', 'phone' => '+221 77 400 50 60',
        'bio' => 'Voyageur régulier Dakar - Paris depuis 5 ans.',
        'id_document' => UploadedFile::fake()->create('cni.pdf', 500, 'application/pdf'),
        'password' => 'secret123', 'password_confirmation' => 'secret123',
    ]);
    Mail::assertSent(GroupeurRegisteredMail::class, function ($m) {
        $m->assertSeeInHtml('48 heures');

        return $m->hasTo('babacar@example.com');
    });
});

test('R9 : e-mail au groupeur quand son compte est validé ou refusé', function () {
    Mail::fake();
    $admin = admin7();
    $ok = groupeur7(GroupeurStatus::en_attente);
    $ko = groupeur7(GroupeurStatus::en_attente);

    $this->actingAs($admin)->post(route('admin.groupeurs.validate', $ok->groupeurProfile));
    Mail::assertSent(GroupeurValidatedMail::class, fn ($m) => $m->hasTo($ok->email));

    $this->actingAs($admin)->post(route('admin.groupeurs.reject', $ko->groupeurProfile), ['rejection_reason' => 'Pièce floue et illisible']);
    Mail::assertSent(GroupeurRejectedMail::class, function ($m) use ($ko) {
        $m->assertSeeInHtml('Pièce floue et illisible');

        return $m->hasTo($ko->email);
    });
});

test('R10 : accusé de réception du signalement envoyé au déclarant', function () {
    Mail::fake();
    $reporter = User::factory()->create(['role' => Role::expediteur]);
    $cible = groupeur7();

    $this->actingAs($reporter)->post(route('reports.store'), [
        'target_user_id' => $cible->id, 'reason' => 'retard_excessif',
        'description' => 'Le départ a été repoussé deux fois sans prévenir.',
    ])->assertRedirect();

    Mail::assertSent(ReportReceivedMail::class, fn ($m) => $m->hasTo($reporter->email));
});

test('R6 : avertissement par e-mail lors d une suspension et via le bouton dédié', function () {
    Mail::fake();
    $admin = admin7();
    $g = groupeur7();

    $this->actingAs($admin)->post(route('admin.users.toggleStatus', $g))->assertRedirect();
    Mail::assertSent(AccountWarningMail::class, fn ($m) => $m->hasTo($g->email));

    Mail::fake();
    $this->actingAs($admin)->post(route('admin.users.warn', $g), ['message' => 'Plusieurs signalements de retard.'])->assertRedirect();
    Mail::assertSent(AccountWarningMail::class, function ($m) use ($g) {
        $m->assertSeeInHtml('Plusieurs signalements de retard.');

        return $m->hasTo($g->email);
    });

    $this->actingAs($g)->post(route('admin.users.warn', $g), ['message' => 'x'])->assertForbidden();
});

test('R6-R10 : aperçu des e-mails réservé à l admin', function () {
    $this->actingAs(admin7())->get(route('admin.emails.index'))->assertOk()->assertSee('Aperçu des e-mails');
    $this->actingAs(admin7())->get(route('admin.emails.show', 'groupeur-inscrit'))->assertOk()->assertSee('48 heures');
    $this->actingAs(User::factory()->create(['role' => Role::expediteur]))->get(route('admin.emails.index'))->assertForbidden();
});

// ───────────── Navigation / pages publiques : 11, 12, 13, 16 ─────────────

test('R12-R13 : boutons Comment ça marche et Trajets fonctionnels', function () {
    $this->get('/')->assertOk()
        ->assertSee('href="'.route('how-it-works').'"', false)
        ->assertSee('href="'.route('trajets.index').'"', false)
        ->assertDontSee('href="#comment-ca-marche"', false)
        ->assertDontSee('href="#trajets"', false);

    $this->get(route('how-it-works'))->assertOk();
    $this->get(route('trajets.index'))->assertOk();

    $exp = User::factory()->create(['role' => Role::expediteur]);
    $this->actingAs($exp)->get(route('expediteur.dashboard'))->assertSee('href="'.route('trajets.index').'"', false);
});

test('R16 : page d accueil enrichie (chiffres, routes populaires, FAQ)', function () {
    $this->get('/')->assertOk()
        ->assertSee('Itinéraires populaires')
        ->assertSee('Questions fréquentes')
        ->assertSee(route('trajets.index', ['origin' => 'Dakar', 'destination' => 'Paris']))
        ->assertSee('Groupeurs vérifiés');
});

test('R11-R17 : espace expéditeur enrichi avec l image de la page de connexion', function () {
    $exp = User::factory()->create(['role' => Role::expediteur]);
    $this->actingAs($exp)->get(route('expediteur.dashboard'))->assertOk()
        ->assertSee('images/fond.jpg')
        ->assertSee('Mes demandes récentes')
        ->assertSee(route('profile.edit'));
});

// ───────────── Villes et pays : 14, 15, 19, 22 ─────────────

test('R14-R22 : API des pays et des villes', function () {
    $this->getJson(route('geo.countries'))->assertOk()->assertJsonFragment(['name' => 'Sénégal'])->assertJsonFragment(['name' => 'France']);

    $this->getJson(route('geo.cities', ['pays' => 'SN']))->assertOk()->assertJsonFragment(['Dakar']);
    $this->getJson(route('geo.cities', ['pays' => 'Sénégal']))->assertOk()->assertJsonFragment(['Thiès']);
    $this->getJson(route('geo.cities', ['pays' => 'FR']))->assertOk()->assertJsonFragment(['Paris'])->assertJsonFragment(['Marseille']);
    $this->getJson(route('geo.cities', ['pays' => 'XX']))->assertNotFound();

    expect(Geo::isValid('Sénégal', 'Dakar'))->toBeTrue();
    expect(Geo::isValid('SN', 'Dakar'))->toBeTrue();
    expect(Geo::isValid('France', 'Dakar'))->toBeFalse();
    expect(Geo::isValid('Sénégal', 'Dakkar'))->toBeFalse();
});

test('R19 : publication de trajet refuse une ville mal orthographiée ou hors du pays', function () {
    $g = groupeur7();
    $base = [
        'origin_country' => 'Sénégal', 'origin_city' => 'Dakar',
        'destination_country' => 'France', 'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'capacity_kg' => 30, 'price_per_kg' => 5000,
    ];

    $this->actingAs($g)->post('/groupeur/trajets', array_merge($base, ['destination_city' => 'Pariss']))
        ->assertSessionHasErrors('destination_city');
    $this->actingAs($g)->post('/groupeur/trajets', array_merge($base, ['origin_city' => 'Paris']))
        ->assertSessionHasErrors('origin_city');
    $this->actingAs($g)->post('/groupeur/trajets', $base)->assertRedirect(route('groupeur.trips.index'));
    expect(Trip::count())->toBe(1);
});

test('R15 : la ville de remise doit faire partie des villes proposées', function () {
    $g = groupeur7();
    $trip = Trip::create([
        'groupeur_id' => $g->id, 'origin_country' => 'Sénégal', 'origin_city' => 'Dakar',
        'destination_country' => 'France', 'destination_city' => 'Paris',
        'departure_date' => now()->addDays(5), 'capacity_kg' => 30, 'price_per_kg' => 5000,
        'status' => \App\Enums\TripStatus::ouvert,
    ]);
    $exp = User::factory()->create(['role' => Role::expediteur]);
    $payload = [
        'description' => 'Vêtements et chaussures', 'category' => 'vetements', 'weight_kg' => 3,
        'receiver_name' => 'Mamadou Ba', 'receiver_phone' => '+33 6 11 22 33 44',
    ];

    $this->actingAs($exp)->post("/expediteur/trajets/{$trip->id}/demande", $payload + ['receiver_city' => 'Pariss'])
        ->assertSessionHasErrors('receiver_city');
    $this->actingAs($exp)->post("/expediteur/trajets/{$trip->id}/demande", $payload + ['receiver_city' => 'Paris'])
        ->assertRedirect(route('expediteur.demandes.index'));
});

test('R19-R22 : formulaires avec sélection pays puis ville via l API', function () {
    $g = groupeur7();
    $this->actingAs($g)->get('/groupeur/trajets/nouveau')->assertOk()
        ->assertSee(route('geo.countries'))
        ->assertSee(route('geo.cities'));

    $exp = User::factory()->create(['role' => Role::expediteur]);
    $this->actingAs($exp)->get(route('expediteur.dashboard'))->assertSee(route('geo.cities'));
    $this->get('/')->assertSee(route('geo.cities'));
});

// ───────────── Objets acceptés / interdits : 20 ─────────────

test('R20 : listes prédéfinies pour objets acceptés et interdits', function () {
    $this->actingAs(groupeur7())->get('/groupeur/trajets/nouveau')->assertOk()
        ->assertSee('Documents administratifs')
        ->assertSee('Vêtements')
        ->assertSee('Produits inflammables')
        ->assertSee('Autre objet (saisie libre)');
});

// ───────────── Traduction : 23 ─────────────

test('R23 : la page d accueil est traduite en anglais', function () {
    $this->withSession(['locale' => 'en'])->get('/')->assertOk()
        ->assertSee('Find a trip')
        ->assertSee('Frequently asked questions')
        ->assertDontSee('Trouver un trajet')
        ->assertDontSee('Questions fréquentes');
});

test('R23 : les espaces connectés sont traduits en anglais', function () {
    $exp = User::factory()->create(['role' => Role::expediteur]);
    $this->actingAs($exp)->withSession(['locale' => 'en'])->get(route('expediteur.dashboard'))
        ->assertOk()->assertSee('Search for a trip')->assertDontSee('Rechercher un trajet');

    $this->actingAs(groupeur7())->withSession(['locale' => 'en'])->get('/groupeur/trajets/nouveau')
        ->assertOk()->assertSee('Publish a new trip')->assertDontSee('Publier un nouveau trajet');

    $this->actingAs(admin7())->withSession(['locale' => 'en'])->get(route('admin.dashboard'))
        ->assertOk()->assertSee('Administrator dashboard')->assertDontSee('Tableau de bord administrateur');

    $this->actingAs(admin7())->withSession(['locale' => 'en'])->get(route('admin.users.index'))
        ->assertOk()->assertSee('User management');
});

test('R23 : le français reste la langue par défaut', function () {
    $this->get('/')->assertSee('Trouver un trajet')->assertDontSee('Find a trip');
});
