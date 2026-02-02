<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Suppression de la table hebergement_reviews (legacy)
     * Cette table n'est plus utilisée - le nouveau système utilise 'reviews'
     */
    public function up(): void
    {
        Schema::dropIfExists('hebergement_reviews');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Table legacy, pas de recreation nécessaire
    }
};
