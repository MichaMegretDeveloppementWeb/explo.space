<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Index spatial : désactivé pour l'instant car nécessite une colonne POINT
        // TODO: Implémenter l'index spatial plus tard avec une colonne coordinates POINT
        Schema::table('places', function (Blueprint $table) {
            $table->index(['latitude', 'longitude'], 'coordinates_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropIndex('coordinates_index');
        });
    }
};
