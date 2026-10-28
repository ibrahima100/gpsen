<?php

namespace Tests\Feature;

use App\Enums\GroupeurStatus;
use App\Enums\Role;
use App\Mail\ReportReceivedMail;
use App\Mail\WarningBeforeAccountClosureMail;
use App\Models\Country;
use App\Models\GroupeurProfile;
use App\Models\Report;
use App\Models\User;
use App\Models\Warning;
use App\Notifications\WarningReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class Remarques9Et10Et15Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CountryAndCitySeeder::class);
    }

    /**
     * Test 9 : Avertissement avant fermeture de compte (action manuelle admin).
     * - Seul un administrateur peut envoyer l'avertissement
     * - Le courriel part avec motif, délai et consignes
     * - L'avertissement est enregistré dans l'historique de la BDD
     * - La notification interne est envoyée au groupeur
     */
    public function test_remarque_9_avertissement_groupeur_par_administrateur(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create(['role' => Role::admin]);
        $groupeur = User::factory()->create(['role' => Role::groupeur, 'email' => 'groupeur@example.com']);
        $profile = GroupeurProfile::create([
            'user_id' => $groupeur->id,
            'id_document_path' => 'documents/fake_cni.jpg',
            'usual_routes' => 'Dakar - Paris',
            'bio' => 'Groupeur certifié assurant la liaison régulière.',
            'validation_status' => GroupeurStatus::valide,
        ]);
        $expediteur = User::factory()->create(['role' => Role::expediteur]);

        // 1. Un non-admin ne peut pas envoyer d'avertissement (403 Forbidden)
        $this->actingAs($expediteur)
            ->post(route('admin.groupeurs.warn', $profile), [
                'reason_preset' => 'Non-respect des engagements de livraison',
                'delay_days' => 7,
            ])
            ->assertForbidden();

        // 2. L'administrateur envoie l'avertissement avec un délai de 7 jours
        $response = $this->actingAs($admin)
            ->post(route('admin.groupeurs.warn', $profile), [
                'reason_preset' => 'Non-respect des engagements de livraison',
                'details' => 'Plusieurs colis ont subi un retard supérieur à 5 jours sans justification.',
                'delay_days' => 7,
            ]);

        $response->assertRedirect(route('admin.groupeurs.show', $profile));
        $response->assertSessionHas('success');

        // 3. Vérification de l'enregistrement dans la table warnings
        $this->assertDatabaseHas('warnings', [
            'groupeur_id' => $groupeur->id,
            'admin_id' => $admin->id,
            'reason' => 'Non-respect des engagements de livraison',
            'delay_days' => 7,
        ]);

        $warning = Warning::first();
        $this->assertNotNull($warning);

        // 4. Vérification que le courriel d'avertissement est envoyé au groupeur
        Mail::assertSent(WarningBeforeAccountClosureMail::class, function ($mail) {
            return $mail->hasTo('groupeur@example.com') &&
                   $mail->warning->delay_days === 7;
        });

        // 5. Vérification de la notification interne
        Notification::assertSentTo($groupeur, WarningReceivedNotification::class);
    }

    /**
     * Test 10 : Accusé de réception d'un signalement.
     * - La personne qui signale reçoit un courriel avec référence
     * - La personne signalée n'est pas prévenue par courriel
     */
    public function test_remarque_10_accuse_de_reception_signalement(): void
    {
        Mail::fake();

        $reporter = User::factory()->create(['role' => Role::expediteur, 'email' => 'reporter@example.com']);
        $targetUser = User::factory()->create(['role' => Role::groupeur, 'email' => 'cible@example.com']);

        $response = $this->actingAs($reporter)
            ->post(route('reports.store'), [
                'target_user_id' => $targetUser->id,
                'reason' => 'comportement_inapproprie',
                'description' => 'Le groupeur a demandé un supplément injustifié lors de la remise.',
            ]);

        $response->assertRedirect(route('dashboard'));

        $report = Report::latest('id')->first();
        $this->assertNotNull($report);

        // Vérification de l'envoi unique à la personne qui signale
        Mail::assertSent(ReportReceivedMail::class, function ($mail) use ($report) {
            return $mail->hasTo('reporter@example.com') &&
                   ! $mail->hasTo('cible@example.com') &&
                   $mail->report->id === $report->id;
        });

        // Aucune notification/courriel vers la cible
        Mail::assertNotSent(ReportReceivedMail::class, fn ($m) => $m->hasTo('cible@example.com'));
    }

    /**
     * Tests 15-17 : Villes et pays avec autocomplétion interne.
     * - Autocomplétion insensible aux accents et à la casse
     * - Rejet d'un identifiant de ville inexistant
     * - Filtrage par pays
     */
    public function test_remarques_15_17_api_villes_et_validation(): void
    {
        $sn = Country::where('code', 'SN')->first();
        $fr = Country::where('code', 'FR')->first();

        // 1. Recherche par autocomplétion tolérante aux accents
        // "thies" sans accent doit trouver "Thiès"
        $response = $this->getJson(route('api.locations.cities', ['q' => 'thies', 'country_id' => $sn->id]));
        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Thiès']);

        // "saint-etienne" sans accent doit trouver "Saint-Étienne" en France
        $response = $this->getJson(route('api.locations.cities', ['q' => 'saint-etienne', 'country_id' => $fr->id]));
        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Saint-Étienne']);

        // 2. Publication trajet rejetée avec une ville inexistante (id = 999999)
        $groupeur = User::factory()->create(['role' => Role::groupeur]);
        GroupeurProfile::create([
            'user_id' => $groupeur->id,
            'id_document_path' => 'documents/fake_cni.jpg',
            'usual_routes' => 'Dakar - Paris',
            'bio' => 'Groupeur régulier.',
            'validation_status' => GroupeurStatus::valide,
        ]);

        $this->actingAs($groupeur)
            ->post(route('groupeur.trips.store'), [
                'origin_city_id' => 999999,
                'destination_city_id' => 888888,
                'departure_date' => now()->addDays(5)->format('Y-m-d'),
                'capacity_kg' => 20,
                'price_per_kg' => 5000,
            ])
            ->assertSessionHasErrors(['origin_city_id', 'destination_city_id']);
    }
}
