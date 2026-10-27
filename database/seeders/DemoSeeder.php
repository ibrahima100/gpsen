<?php

namespace Database\Seeders;

use App\Enums\GroupeurStatus;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Enums\TripStatus;
use App\Models\GroupeurProfile;
use App\Models\Report;
use App\Models\Review;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\RatingService;
use Illuminate\Database\Seeder;

/**
 * Données de démonstration (soutenance) : routes réelles Dakar / Paris / Milan.
 * Idempotent : peut être relancé sans créer de doublons de comptes.
 * Mot de passe commun des comptes démo : Demo@2026
 */
class DemoSeeder extends Seeder
{
    private function user(string $name, string $email, string $phone, Role $role): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->phone = $phone;
        $user->password = 'Demo@2026';
        $user->role = $role;
        $user->status = 'actif';
        $user->locale = 'fr';
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    private function groupeur(string $name, string $email, string $phone, GroupeurStatus $status, string $bio, string $routes, ?string $reason = null): User
    {
        $user = $this->user($name, $email, $phone, Role::groupeur);
        GroupeurProfile::updateOrCreate(['user_id' => $user->id], [
            'bio' => $bio,
            'usual_routes' => $routes,
            'id_document_path' => 'id_documents/demo.pdf',
            'validation_status' => $status,
            'rejection_reason' => $reason,
        ]);

        return $user;
    }

    private function trip(User $g, array $o): Trip
    {
        return Trip::firstOrCreate(
            ['groupeur_id' => $g->id, 'origin_city' => $o['oc'], 'destination_city' => $o['dc'], 'departure_date' => now()->addDays($o['d'])->toDateString()],
            [
                'origin_country' => $o['oco'], 'destination_country' => $o['dco'],
                'arrival_date' => now()->addDays($o['d'] + 1)->toDateString(),
                'capacity_kg' => $o['cap'], 'price_per_kg' => $o['p'],
                'accepted_items' => 'Vêtements, documents, produits cosmétiques scellés, colis secs fermés.',
                'prohibited_items' => 'Produits inflammables, liquides non étanches, denrées fraîches, stupéfiants.',
                'pickup_info' => $o['pick'],
                'status' => $o['status'] ?? TripStatus::ouvert,
            ]
        );
    }

    private function request(Trip $t, User $s, array $o): ShipmentRequest
    {
        return ShipmentRequest::firstOrCreate(
            ['trip_id' => $t->id, 'sender_id' => $s->id, 'description' => $o['desc']],
            [
                'category' => $o['cat'], 'weight_kg' => $o['kg'],
                'receiver_name' => $o['rn'], 'receiver_phone' => $o['rp'], 'receiver_city' => $o['rc'],
                'status' => $o['status'],
                'accepted_at' => $o['status'] === RequestStatus::en_attente ? null : now()->subDays(2),
                'delivered_at' => $o['status'] === RequestStatus::livree ? now()->subDay() : null,
            ]
        );
    }

    public function run(): void
    {
        $fatou = $this->groupeur('Fatou Ndiaye', 'fatou@gpsen.sn', '+221 77 612 34 56', GroupeurStatus::valide,
            'Je fais Dakar–Paris chaque mois pour le travail. Colis soigneusement emballés.', 'Dakar – Paris, Dakar – Milan');
        $moussa = $this->groupeur('Moussa Sow', 'moussa@gpsen.sn', '+221 76 245 78 90', GroupeurStatus::valide,
            'Commerçant entre Thiès, Dakar et Milan depuis 6 ans.', 'Dakar – Milan');
        $this->groupeur('Cheikh Fall', 'cheikh@gpsen.sn', '+221 70 118 22 33', GroupeurStatus::en_attente,
            'Étudiant, voyage deux fois par an vers Paris.', 'Dakar – Paris');
        $this->groupeur('Ibrahima Ba', 'ibrahima@gpsen.sn', '+221 78 904 11 22', GroupeurStatus::refuse,
            'Voyageur occasionnel.', 'Dakar – Lyon', 'Pièce d\'identité illisible : merci de renvoyer un scan net.');

        $aminata = $this->user('Aminata Diop', 'aminata@gpsen.sn', '+221 77 330 45 67', Role::expediteur);
        $omar = $this->user('Omar Gueye', 'omar@gpsen.sn', '+221 76 501 23 45', Role::expediteur);

        $t1 = $this->trip($fatou, ['oc' => 'Dakar', 'oco' => 'SN', 'dc' => 'Paris', 'dco' => 'FR', 'd' => 6, 'cap' => 23, 'p' => 6500, 'pick' => 'Remise aux Almadies ou à Liberté 6, sur rendez-vous.']);
        $t2 = $this->trip($moussa, ['oc' => 'Dakar', 'oco' => 'SN', 'dc' => 'Milan', 'dco' => 'IT', 'd' => 10, 'cap' => 30, 'p' => 5500, 'pick' => 'Collecte à Sacré-Cœur 3.']);
        $this->trip($fatou, ['oc' => 'Paris', 'oco' => 'FR', 'dc' => 'Dakar', 'dco' => 'SN', 'd' => 20, 'cap' => 20, 'p' => 7000, 'pick' => 'Remise à Paris 18e, métro Barbès.']);
        $this->trip($moussa, ['oc' => 'Milan', 'oco' => 'IT', 'dc' => 'Dakar', 'dco' => 'SN', 'd' => 25, 'cap' => 25, 'p' => 5000, 'pick' => 'Remise gare Milano Centrale.']);
        $past = $this->trip($fatou, ['oc' => 'Dakar', 'oco' => 'SN', 'dc' => 'Paris', 'dco' => 'FR', 'd' => -10, 'cap' => 15, 'p' => 6000, 'pick' => 'Almadies.', 'status' => TripStatus::termine]);

        $this->request($t1, $aminata, ['desc' => 'Vêtements et cadeaux pour la famille', 'cat' => 'vetements', 'kg' => 4, 'rn' => 'Awa Diop', 'rp' => '+33 6 12 34 56 78', 'rc' => 'Paris', 'status' => RequestStatus::en_attente]);
        $this->request($t1, $omar, ['desc' => 'Documents administratifs', 'cat' => 'documents', 'kg' => 1, 'rn' => 'Samba Gueye', 'rp' => '+33 6 98 76 54 32', 'rc' => 'Paris', 'status' => RequestStatus::acceptee]);
        $this->request($t2, $aminata, ['desc' => 'Tissus wax', 'cat' => 'vetements', 'kg' => 6, 'rn' => 'Mariama Sy', 'rp' => '+39 345 123 4567', 'rc' => 'Milan', 'status' => RequestStatus::en_transit]);

        $done = $this->request($past, $aminata, ['desc' => 'Produits cosmétiques scellés', 'cat' => 'cosmetiques', 'kg' => 3, 'rn' => 'Khady Diop', 'rp' => '+33 7 11 22 33 44', 'rc' => 'Paris', 'status' => RequestStatus::livree]);
        Review::firstOrCreate(
            ['shipment_request_id' => $done->id, 'author_id' => $aminata->id],
            ['target_id' => $fatou->id, 'rating' => 5, 'comment' => 'Colis livré à temps et en parfait état, très sérieuse.']
        );
        app(RatingService::class)->recalculate($fatou);

        Report::firstOrCreate(
            ['reporter_id' => $omar->id, 'target_user_id' => $moussa->id, 'reason' => 'retard_excessif'],
            ['description' => 'Départ repoussé deux fois sans prévenir à l\'avance.', 'status' => 'ouvert']
        );
    }
}
