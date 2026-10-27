<?php

namespace App\Models;

use App\Enums\GroupeurStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupeurProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groupeur_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'bio',
        'usual_routes',
        'id_document_path',
        'validation_status',
        'rejection_reason',
        'validated_by',
        'validated_at',
        'average_rating',
        'ratings_count',
        'delivered_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'validation_status' => GroupeurStatus::class,
            'validated_at' => 'datetime',
            'average_rating' => 'decimal:2',
            'ratings_count' => 'integer',
            'delivered_count' => 'integer',
        ];
    }

    /**
     * Relationship: The user who owns this groupeur profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The administrator who validated or rejected this profile.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Check if profile validation is pending.
     */
    public function isPending(): bool
    {
        return $this->validation_status === GroupeurStatus::en_attente
            || $this->validation_status === 'en_attente';
    }

    /**
     * Check if profile has been validated by an administrator.
     */
    public function isValidated(): bool
    {
        return $this->validation_status === GroupeurStatus::valide
            || $this->validation_status === 'valide';
    }

    /**
     * Check if profile was rejected by an administrator.
     */
    public function isRefused(): bool
    {
        return $this->validation_status === GroupeurStatus::refuse
            || $this->validation_status === 'refuse';
    }
}
