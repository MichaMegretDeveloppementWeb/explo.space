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
        Schema::create('place_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->foreignId('place_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5); // 'fr' | 'en' | futures langues
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->text('practical_info')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('source_hash', 40)->nullable(); // Pour traduction auto
            $table->timestamps();

            // Contraintes d'unicité
            $table->unique(['place_id', 'locale']);
            $table->unique(['locale', 'slug']);

            // Index pour performance
            $table->index(['locale', 'status']);
            $table->index(['locale', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_translations');
    }
};
