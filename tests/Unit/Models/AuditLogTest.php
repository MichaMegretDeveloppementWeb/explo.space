<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_has_required_relations(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $auditLog = AuditLog::factory()->create([
            'user_id' => $user->id,
            'model_type' => Place::class,
            'model_id' => $place->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $auditLog->user());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class, $auditLog->auditable());
    }

    public function test_audit_log_casts_correctly(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $oldValues = ['title' => 'Old Title', 'description' => 'Old Description'];
        $newValues = ['title' => 'New Title', 'description' => 'New Description'];

        $auditLog = AuditLog::factory()->create([
            'user_id' => $user->id,
            'model_type' => Place::class,
            'model_id' => $place->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        $this->assertIsArray($auditLog->old_values);
        $this->assertIsArray($auditLog->new_values);
        $this->assertEquals($oldValues, $auditLog->old_values);
        $this->assertEquals($newValues, $auditLog->new_values);
    }

    public function test_audit_log_polymorphic_relation(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $auditLog = AuditLog::factory()->create([
            'user_id' => $user->id,
            'model_type' => Place::class,
            'model_id' => $place->id,
            'action' => 'created',
        ]);

        $this->assertEquals($place->id, $auditLog->auditable->id);
        $this->assertInstanceOf(Place::class, $auditLog->auditable);
    }
}
