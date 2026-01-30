<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'mentions-legales',
                'title' => 'Mentions légales',
                'content' => 'Contenu des mentions légales...',
                'is_published' => true
            ],
            [
                'slug' => 'cgu',
                'title' => 'Conditions générales d\'utilisation',
                'content' => 'Contenu des CGU...',
                'is_published' => true
            ],
            [
                'slug' => 'confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => 'Contenu de la politique de confidentialité...',
                'is_published' => true
            ],
            [
                'slug' => 'cookies',
                'title' => 'Politique de cookies',
                'content' => 'Contenu de la politique de cookies...',
                'is_published' => true
            ]
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
