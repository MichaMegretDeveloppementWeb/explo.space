<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has the strict 'admin' role (not super_admin)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has admin rights (admin or super_admin)
     */
    public function hasAdminRights(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Check if user is specifically a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * @return HasMany<PlaceRequest, $this>
     */
    public function viewedPlaceRequests(): HasMany
    {
        return $this->hasMany(PlaceRequest::class, 'viewed_by_admin_id');
    }

    /**
     * @return HasMany<PlaceRequest, $this>
     */
    public function processedPlaceRequests(): HasMany
    {
        return $this->hasMany(PlaceRequest::class, 'processed_by_admin_id');
    }

    /**
     * @return HasMany<EditRequest, $this>
     */
    public function viewedEditRequests(): HasMany
    {
        return $this->hasMany(EditRequest::class, 'viewed_by_admin_id');
    }

    /**
     * @return HasMany<EditRequest, $this>
     */
    public function processedEditRequests(): HasMany
    {
        return $this->hasMany(EditRequest::class, 'processed_by_admin_id');
    }

    /**
     * @return HasMany<Place, $this>
     */
    public function managedPlaces(): HasMany
    {
        return $this->hasMany(Place::class, 'admin_id');
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
