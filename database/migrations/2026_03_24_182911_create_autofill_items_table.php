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
        Schema::create('autofill_items', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->foreignId('workflow_id')->constrained('autofill_workflows')->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('discovered');
            $table->string('step_failed_at')->nullable();
            $table->json('enrichment_data')->nullable();
            $table->json('images_data')->nullable();
            $table->foreignId('place_id')->nullable()->constrained('places')->nullOnDelete();
            $table->unsignedInteger('tokens_in')->default(0);
            $table->unsignedInteger('tokens_out')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->unsignedInteger('images_count')->default(0);
            $table->text('error_message')->nullable();
            $table->text('error_technical')->nullable();
            $table->text('suggested_tags')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('workflow_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autofill_items');
    }
};
