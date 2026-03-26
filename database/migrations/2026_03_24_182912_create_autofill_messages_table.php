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
        Schema::create('autofill_messages', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->foreignId('workflow_id')->constrained('autofill_workflows')->cascadeOnDelete();
            $table->string('type'); // text, selection, progress, images, recap, error
            $table->string('role'); // system, user
            $table->json('payload');
            $table->timestamp('created_at');

            $table->index('workflow_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autofill_messages');
    }
};
