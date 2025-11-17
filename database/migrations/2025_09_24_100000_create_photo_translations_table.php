<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('photo_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->foreignId('photo_id')->constrained('photos')->cascadeOnDelete();
            $table->string('locale', 5); // 'fr' | 'en' | futures langues
            $table->string('alt_text', 125)->nullable();
            $table->timestamps();

            // Contraintes d'unicité
            $table->unique(['photo_id', 'locale']);

            // Index pour performance
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_translations');
    }
};
