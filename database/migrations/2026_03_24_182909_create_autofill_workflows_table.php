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
        Schema::create('autofill_workflows', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_general_ci');

            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('query');
            $table->string('provider'); // openai, anthropic, perplexity
            $table->unsignedInteger('requested_quantity');
            $table->string('status')->default('pending');
            $table->unsignedInteger('total_tokens_in')->default(0);
            $table->unsignedInteger('total_tokens_out')->default(0);
            $table->decimal('total_cost', 10, 6)->default(0);
            $table->text('error_message')->nullable();
            $table->text('error_technical')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autofill_workflows');
    }
};
