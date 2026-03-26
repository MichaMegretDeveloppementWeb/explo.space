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
        Schema::create('autofill_step_logs', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->foreignId('workflow_id')->constrained('autofill_workflows')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('autofill_items')->cascadeOnDelete();
            $table->string('step');
            $table->json('input_data');
            $table->longText('raw_output');
            $table->unsignedInteger('tokens_in')->default(0);
            $table->unsignedInteger('tokens_out')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->string('model');
            $table->timestamp('created_at');

            $table->index('workflow_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autofill_step_logs');
    }
};
