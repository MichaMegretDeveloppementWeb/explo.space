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
        Schema::table('autofill_workflows', function (Blueprint $table) {
            $table->string('state')->default('active')->after('status');
            $table->index('state');
        });

        // Migrate existing data: derive state from old status values
        // 'failed' → state=paused, status=pending (step unknown)
        DB::table('autofill_workflows')
            ->where('status', 'failed')
            ->update(['state' => 'paused', 'status' => 'pending']);

        // 'interrupted' → state=abandoned, status=pending (step unknown)
        DB::table('autofill_workflows')
            ->where('status', 'interrupted')
            ->update(['state' => 'abandoned', 'status' => 'pending']);

        // 'completed' → state=completed
        DB::table('autofill_workflows')
            ->where('status', 'completed')
            ->update(['state' => 'completed']);

        // All others stay state=active (default)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autofill_workflows', function (Blueprint $table) {
            $table->dropIndex(['state']);
            $table->dropColumn('state');
        });
    }
};
