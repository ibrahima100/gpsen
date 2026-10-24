<?php

use App\Enums\Role;
use App\Models\Report;
use App\Models\User;
use App\Notifications\ReviewReceivedNotification;

test('Etape 6 : Cloche et centre de notifications', function () {
    $user = User::factory()->create(['role' => Role::expediteur]);

    // Simuler une notification en base
    $user->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid()->toString(),
        'type' => ReviewReceivedNotification::class,
        'data' => [
            'title' => 'Test Notification',
            'message' => 'Contenu de notification',
            'action_url' => url('/expediteur/demandes'),
        ],
        'read_at' => null,
    ]);

    expect($user->unreadNotifications->count())->toBe(1);

    // Accès au centre de notifications
    $this->actingAs($user)->get(route('notifications.index'))
        ->assertStatus(200)
        ->assertSee('Test Notification')
        ->assertSee('Contenu de notification');

    // Marquer tout comme lu
    $this->actingAs($user)->post(route('notifications.markAllRead'))
        ->assertRedirect();

    $user->refresh();
    expect($user->unreadNotifications->count())->toBe(0);
});

test('Etape 6 : Formulaire de signalement par un utilisateur', function () {
    $reporter = User::factory()->create(['role' => Role::expediteur]);
    $targetUser = User::factory()->create(['role' => Role::groupeur]);

    $this->actingAs($reporter)->get(route('reports.create', ['user_id' => $targetUser->id]))
        ->assertStatus(200)
        ->assertSee($targetUser->name);

    $this->actingAs($reporter)->post(route('reports.store'), [
        'target_user_id' => $targetUser->id,
        'reason' => 'comportement_inapproprie',
        'description' => 'Comportement irrespectueux et messages déplacés reçus sur WhatsApp.',
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('reports', [
        'reporter_id' => $reporter->id,
        'target_user_id' => $targetUser->id,
        'reason' => 'comportement_inapproprie',
        'status' => 'ouvert',
    ]);
});

test('Etape 6 : Modération des signalements par l administrateur', function () {
    $admin = User::factory()->create(['role' => Role::admin]);
    $reporter = User::factory()->create(['role' => Role::expediteur]);
    $target = User::factory()->create(['role' => Role::groupeur]);

    $report = Report::create([
        'reporter_id' => $reporter->id,
        'target_user_id' => $target->id,
        'reason' => 'suspicion_fraude',
        'description' => 'Faux document supposé sur ce compte voyageur.',
        'status' => 'ouvert',
    ]);

    // Admin consulte la file de signalements
    $this->actingAs($admin)->get(route('admin.reports.index'))
        ->assertStatus(200)
        ->assertSee('Suspicion fraude');

    // Admin consulte le détail
    $this->actingAs($admin)->get(route('admin.reports.show', $report))
        ->assertStatus(200)
        ->assertSee('Faux document supposé')
        ->assertSee($target->name);

    // Admin traite le signalement avec une note interne
    $this->actingAs($admin)->post(route('admin.reports.update', $report), [
        'status' => 'traite',
        'admin_note' => 'Vérification effectuée avec les autorités. Affaire résolue.',
    ])->assertRedirect();

    $report->refresh();
    expect($report->status)->toBe('traite');
    expect($report->admin_note)->toBe('Vérification effectuée avec les autorités. Affaire résolue.');
    expect($report->handled_by)->toBe($admin->id);
});

test('Etape 6 : Supervision et suspension/reactivation des utilisateurs par l admin', function () {
    $admin = User::factory()->create(['role' => Role::admin]);
    $user = User::factory()->create(['role' => Role::groupeur, 'status' => 'actif']);

    // Admin liste les utilisateurs
    $this->actingAs($admin)->get(route('admin.users.index'))
        ->assertStatus(200)
        ->assertSee($user->name);

    // Admin suspend l'utilisateur
    $this->actingAs($admin)->post(route('admin.users.toggleStatus', $user))
        ->assertRedirect();

    $user->refresh();
    expect($user->status)->toBe('suspendu');

    // Admin ne peut pas suspendre son propre compte
    $this->actingAs($admin)->post(route('admin.users.toggleStatus', $admin))
        ->assertSessionHas('error');

    $admin->refresh();
    expect($admin->status)->toBe('actif');

    // Admin réactive l'utilisateur
    $this->actingAs($admin)->post(route('admin.users.toggleStatus', $user))
        ->assertRedirect();

    $user->refresh();
    expect($user->status)->toBe('actif');
});
