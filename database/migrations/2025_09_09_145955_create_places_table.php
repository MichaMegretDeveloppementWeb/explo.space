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
        Schema::create('places', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->decimal('latitude', 9, 6); // Précision au mètre (6 décimales)
            $table->decimal('longitude', 9, 6); // Précision au mètre (6 décimales)
            $table->geometry('coordinates', 'point');
            $table->string('address')->nullable();
            $table->boolean('is_featured')->default(false); // Pour "références à la une" page d'accueil
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade'); // Admin qui a créé/validé ce lieu
            $table->unsignedBigInteger('request_id')->nullable();
            $table->foreign('request_id')->references('id')->on('place_requests')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();

            $table->index(['is_featured']);
            $table->index(['latitude', 'longitude']); // Index classique pour compatibilité
            $table->spatialIndex('coordinates'); // Index spatial pour performance optimale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
