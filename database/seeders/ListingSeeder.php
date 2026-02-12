<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partner = User::where('role', 'partenaire')->first();

        if (!$partner) {
            return;
        }

        $listings = [
            [
                'user_id' => $partner->id,
                'title' => 'Villa de Luxe avec Piscine',
                'description' => 'Une magnifique villa située au bord de la mer, parfaite pour des vacances relaxantes.',
                'price_per_night' => 250.00,
                'address' => '123 Avenue des Plages',
                'city' => 'Nice',
                'country' => 'France',
                'max_guests' => 6,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'is_active' => true
            ],
            [
                'user_id' => $partner->id,
                'title' => 'Appartement Moderne Centre-Ville',
                'description' => 'Un appartement élégant et moderne en plein cœur de la ville, proche de toutes commodités.',
                'price_per_night' => 120.00,
                'address' => '45 Rue de la République',
                'city' => 'Paris',
                'country' => 'France',
                'max_guests' => 4,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'is_active' => true
            ],
            [
                'user_id' => $partner->id,
                'title' => 'Chalet à la Montagne',
                'description' => 'Chalet chaleureux avec vue imprenable sur les Alpes, idéal pour le ski et la randonnée.',
                'price_per_night' => 180.00,
                'address' => '78 Chemin des Cimes',
                'city' => 'Chamonix',
                'country' => 'France',
                'max_guests' => 8,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'is_active' => true
            ]
        ];

        foreach ($listings as $listing) {
            Listing::create($listing);
        }
    }
}
