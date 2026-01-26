<?php

namespace Database\Seeders;

use App\Models\Hebergement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HebergementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hebergements = [
            [
                'name' => 'Hôtel de Luxe Paris',
                'location' => 'Paris, France',
                'price' => 150.00,
                'rating' => 4.5,
                'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                'type' => 'hotel',
                'capacity' => 2,
                'description' => 'Un magnifique hôtel 5 étoiles au cœur de Paris, avec vue sur la Tour Eiffel.',
                'amenities' => ['WiFi', 'Piscine', 'Spa', 'Room Service', 'Parking']
            ],
            [
                'name' => 'Appartement Moderne Lyon',
                'location' => 'Lyon, France',
                'price' => 80.00,
                'rating' => 4.2,
                'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
                'type' => 'appartement',
                'capacity' => 4,
                'description' => 'Appartement moderne et spacieux dans le centre-ville de Lyon, proche de tous les commerces.',
                'amenities' => ['WiFi', 'Cuisine équipée', 'Lave-linge', 'Télévision']
            ],
            [
                'name' => 'Villa Méditerranéenne',
                'location' => 'Nice, France',
                'price' => 250.00,
                'rating' => 4.8,
                'image' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800',
                'type' => 'villa',
                'capacity' => 8,
                'description' => 'Superbe villa avec piscine privée et vue sur la mer Méditerranée.',
                'amenities' => ['Piscine', 'Jardin', 'BBQ', 'WiFi', 'Parking', 'Climatisation']
            ],
            [
                'name' => 'Lodge Nature',
                'location' => 'Chamonix, France',
                'price' => 120.00,
                'rating' => 4.6,
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'type' => 'lodge',
                'capacity' => 6,
                'description' => 'Lodge confortable au pied des montagnes, idéal pour les amateurs de ski et de randonnée.',
                'amenities' => ['Cheminée', 'WiFi', 'Parking', 'Vue montagne']
            ],
            [
                'name' => 'Camping Les Pins',
                'location' => 'Biarritz, France',
                'price' => 35.00,
                'rating' => 4.0,
                'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=800',
                'type' => 'camping',
                'capacity' => 4,
                'description' => 'Camping familial à proximité de la plage, avec sanitaires modernes et aire de jeux.',
                'amenities' => ['Sanitaires', 'Douches', 'Aire de jeux', 'Boutique', 'WiFi']
            ],
        ];

        foreach ($hebergements as $hebergement) {
            Hebergement::create($hebergement);
        }
    }
}
