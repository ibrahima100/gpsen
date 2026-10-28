<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class CountryAndCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'SN' => [
                'name' => 'Sénégal',
                'cities' => [
                    'Dakar', 'Thiès', 'Saint-Louis', 'Kaolack', 'Ziguinchor', 'Mbour', 'Touba',
                    'Rufisque', 'Kolda', 'Fatick', 'Tambacounda', 'Louga', 'Diourbel', 'Sédhiou',
                    'Kédougou', 'Matam', 'Tivaouane', 'Joal-Fadiouth', 'Bambey', 'Dagana', 'Cap Skirring',
                ],
            ],
            'FR' => [
                'name' => 'France',
                'cities' => [
                    'Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Montpellier',
                    'Strasbourg', 'Bordeaux', 'Lille', 'Rennes', 'Reims', 'Saint-Étienne', 'Toulon',
                    'Le Havre', 'Grenoble', 'Dijon', 'Angers', 'Nîmes', 'Villeurbanne', 'Le Mans',
                    'Aix-en-Provence', 'Clermont-Ferrand', 'Brest', 'Tours', 'Amiens', 'Limoges',
                    'Perpignan', 'Boulogne-Billancourt', 'Metz', 'Besançon', 'Orléans', 'Saint-Denis', 'Argenteuil',
                ],
            ],
            'IT' => [
                'name' => 'Italie',
                'cities' => [
                    'Rome', 'Milan', 'Naples', 'Turin', 'Palerme', 'Gênes', 'Bologne', 'Florence',
                    'Bari', 'Catane', 'Venise', 'Vérone', 'Messine', 'Padoue', 'Trieste', 'Brescia',
                    'Parme', 'Bergame',
                ],
            ],
            'ES' => [
                'name' => 'Espagne',
                'cities' => [
                    'Madrid', 'Barcelone', 'Valence', 'Séville', 'Saragosse', 'Malaga', 'Murcie',
                    'Palma', 'Las Palmas', 'Bilbao', 'Alicante', 'Cordoue', 'Valladolid',
                ],
            ],
            'BE' => [
                'name' => 'Belgique',
                'cities' => [
                    'Bruxelles', 'Anvers', 'Gand', 'Charleroi', 'Liège', 'Bruges', 'Namur',
                    'Louvain', 'Mons', 'Alost',
                ],
            ],
            'US' => [
                'name' => 'États-Unis',
                'cities' => [
                    'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphie',
                    'San Antonio', 'San Diego', 'Dallas', 'Austin', 'Jacksonville', 'Fort Worth',
                    'Columbus', 'Charlotte', 'San Francisco', 'Indianapolis', 'Seattle', 'Denver',
                    'Washington', 'Boston', 'Atlanta', 'Miami',
                ],
            ],
            'CA' => [
                'name' => 'Canada',
                'cities' => [
                    'Montréal', 'Toronto', 'Vancouver', 'Calgary', 'Edmonton', 'Ottawa',
                    'Québec', 'Winnipeg', 'Hamilton', 'Halifax',
                ],
            ],
            'CI' => [
                'name' => 'Côte d\'Ivoire',
                'cities' => [
                    'Abidjan', 'Bouaké', 'Yamoussoukro', 'San-Pédro', 'Korhogo', 'Daloa',
                    'Man', 'Gagnoa', 'Soubré',
                ],
            ],
            'MA' => [
                'name' => 'Maroc',
                'cities' => [
                    'Casablanca', 'Rabat', 'Fès', 'Tanger', 'Marrakech', 'Salé', 'Meknès',
                    'Agadir', 'Oujda', 'Kénitra',
                ],
            ],
        ];

        foreach ($data as $code => $countryInfo) {
            $country = Country::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $countryInfo['name'],
                    'normalized_name' => Country::normalizeString($countryInfo['name']),
                ]
            );

            foreach ($countryInfo['cities'] as $cityName) {
                City::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $cityName,
                    ],
                    [
                        'normalized_name' => City::normalizeString($cityName),
                    ]
                );
            }
        }

        $this->linkExistingTrips();
        $this->linkExistingShipmentRequests();
    }

    protected function linkExistingTrips(): void
    {
        $trips = Trip::whereNull('origin_city_id')->orWhereNull('destination_city_id')->get();

        foreach ($trips as $trip) {
            if (! $trip->origin_city_id && $trip->origin_city) {
                $normalized = City::normalizeString($trip->origin_city);
                $found = City::where('normalized_name', $normalized)->first();
                if ($found) {
                    $trip->origin_city_id = $found->id;
                }
            }

            if (! $trip->destination_city_id && $trip->destination_city) {
                $normalized = City::normalizeString($trip->destination_city);
                $found = City::where('normalized_name', $normalized)->first();
                if ($found) {
                    $trip->destination_city_id = $found->id;
                }
            }

            $trip->saveQuietly();
        }
    }

    protected function linkExistingShipmentRequests(): void
    {
        $requests = ShipmentRequest::whereNull('receiver_city_id')->get();

        foreach ($requests as $req) {
            if (! $req->receiver_city_id && $req->receiver_city) {
                $normalized = City::normalizeString($req->receiver_city);
                $found = City::where('normalized_name', $normalized)->first();
                if ($found) {
                    $req->receiver_city_id = $found->id;
                }
            }

            $req->saveQuietly();
        }
    }
}
