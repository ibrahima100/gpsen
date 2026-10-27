<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Role and status are explicitly excluded from mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
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
            'role' => Role::class,
            'status' => 'string',
        ];
    }

    /**
     * Relationship: A user (if groupeur) has one profile.
     */
    public function groupeurProfile(): HasOne
    {
        return $this->hasOne(GroupeurProfile::class);
    }

    /**
     * Relationship: A groupeur has many trips.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'groupeur_id');
    }

    /**
     * Relationship: An expediteur has many shipment requests.
     */
    public function shipmentRequests(): HasMany
    {
        return $this->hasMany(ShipmentRequest::class, 'sender_id');
    }

    /**
     * Relationship: Reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'author_id');
    }

    /**
     * Alias for reviews written by this user.
     */
    public function reviewsAuthored(): HasMany
    {
        return $this->hasMany(Review::class, 'author_id');
    }

    /**
     * Relationship: Reviews received by this user (typically as target/groupeur).
     */
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'target_id', 'id');
    }

    /**
     * Relationship: Reports submitted by this user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === Role::admin;
    }

    /**
     * Check if user is a groupeur.
     */
    public function isGroupeur(): bool
    {
        return $this->role === Role::groupeur;
    }

    /**
     * Check if user is an expediteur.
     */
    public function isExpediteur(): bool
    {
        return $this->role === Role::expediteur;
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'actif';
    }

    /**
     * Check if user account is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspendu';
    }

    /**
     * Check if the user is a validated groupeur.
     */
    public function isGroupeurValidated(): bool
    {
        return $this->isGroupeur()
            && $this->groupeurProfile !== null
            && ($this->groupeurProfile->validation_status === 'valide'
                || (method_exists($this->groupeurProfile, 'isValidated') && $this->groupeurProfile->isValidated()));
    }
}
