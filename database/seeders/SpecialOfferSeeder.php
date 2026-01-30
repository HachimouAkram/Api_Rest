<?php

namespace Database\Seeders;

use App\Models\SpecialOffer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offers = [
            [
                'title' => 'Offre été -20%',
                'description' => 'Profitez de -20% sur toutes les réservations d\'été',
                'discount_percentage' => 20.00,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(30),
                'is_active' => true
            ],
            [
                'title' => 'Week-end prolongé',
                'description' => '3 nuits pour le prix de 2',
                'discount_percentage' => 33.33,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(60),
                'is_active' => true
            ],
            [
                'title' => 'Séjour romantique',
                'description' => 'Bouteille de champagne offerte',
                'discount_percentage' => 10.00,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(15),
                'is_active' => true
            ]
        ];

        foreach ($offers as $offer) {
            SpecialOffer::create($offer);
        }
    }
}
