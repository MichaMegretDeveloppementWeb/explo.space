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
        Schema::table('edit_requests', function (Blueprint $table) {
            // Ajouter colonne JSON pour stocker les champs/photos appliqués lors de l'acceptation
            // Structure: {"fields": ["title", "description"], "photos": [0, 2]}
            $table->json('applied_changes')->nullable()->after('suggested_changes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edit_requests', function (Blueprint $table) {
            $table->dropColumn('applied_changes');
        });
    }
};
