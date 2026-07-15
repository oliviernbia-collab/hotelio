<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Hotel;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@hotelio.ci'], [
            'name'     => 'Manager Hôtelio',
            'password' => Hash::make('olivier1999'),
            'role'     => 'admin',
            'status'   => 'actif',
            'hotels'   => 'Toutes les propriétés',
        ]);

        // ── Prestataire demo ───────────────────────────────────────────────
        $prestataire = User::firstOrCreate(['email' => 'prestataire@hotelio.ci'], [
            'name'     => 'Konan Yves',
            'password' => Hash::make('password123'),
            'role'     => 'prestataire',
            'status'   => 'actif',
            'hotels'   => 'Hôtels partenaires',
        ]);

        // ── Client demo ────────────────────────────────────────────────────
        $clientUser = User::firstOrCreate(['email' => 'client@hotelio.ci'], [
            'name'     => 'Aïssé Traoré',
            'password' => Hash::make('password123'),
            'role'     => 'client',
            'status'   => 'actif',
            'hotels'   => '',
        ]);

        // ── Hôtels ────────────────────────────────────────────────────────
        $hotelsData = [
            [
                'user_id'     => $prestataire->id,
                'name'        => 'Hôtel Émeraude',
                'short_name'  => 'Émeraude',
                'city'        => 'Abidjan',
                'quartier'    => 'Cocody',
                'stars'       => 5,
                'rooms'       => 56,
                'category'    => 'Luxe',
                'status'      => 'Ouvert',
                'rating'      => 4.9,
                'price'       => 320000,
                'emoji'       => '🏨',
                'color'       => '#4f46e5',
                'manager'     => 'Olivier N.',
                'phone'       => '+225 07 83 45 67 89',
                'email'       => 'emerald@hotelio.ci',
                'revenue'     => 129000000,
                'occupancy'   => 89,
                'checkins'    => 128,
                'checkouts'   => 112,
                'description' => 'Un palace moderne au cœur de la ville, idéal pour les voyageurs d\'affaires et de loisir. WiFi haut débit, spa, restaurant gastronomique et piscine à débordement.',
                'services'    => ['WiFi', 'Spa', 'Piscine', 'Restaurant', 'Parking', 'Gym', 'Bar'],
                'opened_at'   => '2018-06-15',
            ],
            [
                'user_id'     => $prestataire->id,
                'name'        => 'Villa Sable',
                'short_name'  => 'Sable',
                'city'        => 'Grand-Bassam',
                'quartier'    => 'Plage',
                'stars'       => 4,
                'rooms'       => 32,
                'category'    => 'Plage',
                'status'      => 'Ouvert',
                'rating'      => 4.6,
                'price'       => 175000,
                'emoji'       => '🏖️',
                'color'       => '#f97316',
                'manager'     => 'Aïssata F.',
                'phone'       => '+225 05 55 22 11 00',
                'email'       => 'sable@hotelio.ci',
                'revenue'     => 82000000,
                'occupancy'   => 76,
                'checkins'    => 92,
                'checkouts'   => 84,
                'description' => 'Escale balnéaire avec vue panoramique et activités de plage adaptées à chaque séjour. Bungalows luxueux face à l\'Océan Atlantique.',
                'services'    => ['WiFi', 'Plage privée', 'Piscine', 'Restaurant', 'Bar', 'Sports nautiques'],
                'opened_at'   => '2020-02-05',
            ],
            [
                'user_id'     => $admin->id,
                'name'        => 'Résidence Kpêna',
                'short_name'  => 'Kpêna',
                'city'        => 'San Pedro',
                'quartier'    => 'Centre-ville',
                'stars'       => 4,
                'rooms'       => 24,
                'category'    => 'Affaires',
                'status'      => 'Ouvert',
                'rating'      => 4.4,
                'price'       => 145000,
                'emoji'       => '🏢',
                'color'       => '#0ea5e9',
                'manager'     => 'Yao K.',
                'phone'       => '+225 09 47 11 22 33',
                'email'       => 'kpema@hotelio.ci',
                'revenue'     => 54000000,
                'occupancy'   => 68,
                'checkins'    => 75,
                'checkouts'   => 68,
                'description' => 'Hôtel d\'affaires avec salles de réunion et services personnalisés pour les professionnels en déplacement.',
                'services'    => ['WiFi', 'Salles de réunion', 'Restaurant d\'affaires', 'Navette aéroport', 'Parking'],
                'opened_at'   => '2019-09-01',
            ],
            [
                'user_id'     => $prestataire->id,
                'name'        => 'Hôtel du Plateau',
                'short_name'  => 'Plateau',
                'city'        => 'Abidjan',
                'quartier'    => 'Plateau',
                'stars'       => 5,
                'rooms'       => 80,
                'category'    => 'Luxe',
                'status'      => 'Ouvert',
                'rating'      => 4.8,
                'price'       => 280000,
                'emoji'       => '🌟',
                'color'       => '#8b5cf6',
                'manager'     => 'Ibrahim D.',
                'phone'       => '+225 07 00 11 22 33',
                'email'       => 'plateau@hotelio.ci',
                'revenue'     => 98000000,
                'occupancy'   => 82,
                'checkins'    => 155,
                'checkouts'   => 144,
                'description' => 'Hôtel de prestige au cœur du quartier des affaires d\'Abidjan, avec vue panoramique sur la lagune.',
                'services'    => ['WiFi', 'Spa', 'Piscine panoramique', 'Restaurant gastronomique', 'Business center', 'Conciergerie'],
                'opened_at'   => '2015-03-20',
            ],
            [
                'user_id'     => $prestataire->id,
                'name'        => 'Hôtel Yamoussoukro Palace',
                'short_name'  => 'Yam Palace',
                'city'        => 'Yamoussoukro',
                'quartier'    => 'Centre',
                'stars'       => 4,
                'rooms'       => 45,
                'category'    => 'Luxe',
                'status'      => 'Ouvert',
                'rating'      => 4.5,
                'price'       => 190000,
                'emoji'       => '🏛️',
                'color'       => '#10b981',
                'manager'     => 'Kouassi B.',
                'phone'       => '+225 09 55 44 33 22',
                'email'       => 'yam.palace@hotelio.ci',
                'revenue'     => 61000000,
                'occupancy'   => 72,
                'checkins'    => 98,
                'checkouts'   => 90,
                'description' => 'Au cœur de la capitale politique ivoirienne, idéalement situé près de la Basilique Notre-Dame de la Paix.',
                'services'    => ['WiFi', 'Piscine', 'Restaurant', 'Bar', 'Tennis', 'Parking'],
                'opened_at'   => '2017-11-10',
            ],
        ];

        foreach ($hotelsData as $hd) {
            $hotel = Hotel::firstOrCreate(['name' => $hd['name']], $hd);

            // Créer des chambres pour chaque hôtel
            $types = ['Standard', 'Deluxe', 'Suite'];
            $floors = (int) ceil($hotel->rooms / 16);
            for ($f = 1; $f <= min($floors, 3); $f++) {
                for ($n = 1; $n <= min(4, $hotel->rooms); $n++) {
                    $type = $types[min($f - 1, 2)];
                    $multiplier = $f === 1 ? 0.75 : ($f === 2 ? 1.0 : 1.5);
                    Room::firstOrCreate(
                        ['hotel_id' => $hotel->id, 'number' => sprintf('%d%02d', $f, $n)],
                        [
                            'floor'       => $f,
                            'type'        => $type,
                            'status'      => $n % 3 === 0 ? 'occupée' : 'disponible',
                            'price'       => (int) round($hotel->price * $multiplier),
                            'capacity'    => $f >= 3 ? 4 : 2,
                            'description' => "Chambre $type - Étage $f",
                        ]
                    );
                }
            }
        }

        // ── Clients ───────────────────────────────────────────────────────
        $clientsData = [
            ['user_id' => $clientUser->id, 'code' => 'CLI-129', 'first_name' => 'Aïssé', 'last_name' => 'Traoré', 'email' => 'client@hotelio.ci', 'phone' => '+225 07 11 22 33 44', 'city' => 'Abidjan', 'quartier' => 'Cocody', 'total_stays' => 7, 'total_spend' => 3300000, 'status' => 'VIP', 'nationality' => 'Ivoirienne', 'last_visit' => '2024-03-12'],
            ['code' => 'CLI-533', 'first_name' => 'Jean', 'last_name' => 'Kouassi', 'email' => 'jean.k@example.com', 'phone' => '+225 09 11 22 33 55', 'city' => 'Yamoussoukro', 'quartier' => 'Centre', 'total_stays' => 3, 'total_spend' => 850000, 'status' => 'Actif', 'nationality' => 'Ivoirienne', 'last_visit' => '2024-03-30'],
            ['code' => 'CLI-207', 'first_name' => 'Sara', 'last_name' => 'Zongo', 'email' => 'sara.z@example.com', 'phone' => '+225 07 22 33 44 55', 'city' => 'Bouaké', 'quartier' => 'Riviera', 'total_stays' => 5, 'total_spend' => 1250000, 'status' => 'Fidèle', 'nationality' => 'Burkinabé', 'last_visit' => '2024-04-10'],
        ];

        foreach ($clientsData as $cd) {
            Client::firstOrCreate(['code' => $cd['code']], $cd);
        }

        // ── Produits ──────────────────────────────────────────────────────
        $productsData = [
            ['name' => 'Panier gourmand', 'price' => 52000, 'stock' => 22, 'category' => 'Cadeau', 'emoji' => '🎁', 'description' => 'Sélection de douceurs locales pour les clients.'],
            ['name' => 'Massage détente', 'price' => 70000, 'stock' => 14, 'category' => 'Bien-être', 'emoji' => '💆', 'description' => 'Séance de massage relaxant en chambre.'],
            ['name' => 'Cocktail plage', 'price' => 13500, 'stock' => 45, 'category' => 'Boisson', 'emoji' => '🍹', 'description' => 'Boisson rafraîchissante servie au bord de la piscine.'],
            ['name' => 'Kit bien-être', 'price' => 35000, 'stock' => 30, 'category' => 'Bien-être', 'emoji' => '🧴', 'description' => 'Produits de soin et aromathérapie premium.'],
            ['name' => 'Petit-déjeuner VIP', 'price' => 25000, 'stock' => 100, 'category' => 'Restauration', 'emoji' => '🥐', 'description' => 'Petit-déjeuner continental luxueux en chambre.'],
        ];

        foreach ($productsData as $pd) {
            Product::firstOrCreate(['name' => $pd['name']], $pd);
        }

        // ── Réservation demo ──────────────────────────────────────────────
        $firstHotel  = Hotel::first();
        $firstClient = Client::first();

        if ($firstHotel && $firstClient) {
            Reservation::firstOrCreate(['code' => 'RES-001'], [
                'client_id'      => $firstClient->id,
                'hotel_id'       => $firstHotel->id,
                'room'           => '101',
                'checkin'        => now()->addDays(5)->toDateString(),
                'checkout'       => now()->addDays(9)->toDateString(),
                'nights'         => 4,
                'price'          => 125000,
                'total'          => 500000,
                'status'         => 'confirmée',
                'channel'        => 'Web',
                'paid'           => true,
                'payment_method' => 'Orange Money',
                'nationality'    => 'Ivoirienne',
            ]);
        }
    }
}
