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
        Schema::create('edit_requests', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->string('contact_email'); // Email pour suivi et notification (pas de compte utilisateur)
            $table->string('detected_language', 5)->nullable(); // Langue détectée automatiquement
            $table->foreignId('place_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['modification', 'signalement'])->default('modification');
            $table->text('description');
            $table->json('suggested_changes')->nullable();
            $table->enum('status', ['submitted', 'pending', 'accepted', 'refused'])->default('submitted');
            $table->text('admin_reason')->nullable();
            $table->foreignId('viewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin qui a vu en premier
            $table->foreignId('processed_by_admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin qui a traité
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['place_id', 'status']);
            $table->index(['contact_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edit_requests');
    }
};
