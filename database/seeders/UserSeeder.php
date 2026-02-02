<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un admin
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        // Créer un partenaire
        User::create([
            'name' => 'Partner Test',
            'email' => 'partner@test.com',
            'password' => Hash::make('password123'),
            'role' => 'partenaire'
        ]);

        // Créer un client
        User::create([
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'password' => Hash::make('password123'),
            'role' => 'client'
        ]);
    }
}
