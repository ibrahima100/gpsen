<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShipmentRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipment_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'trip_id',
        'sender_id',
        'description',
        'category',
        'weight_kg',
        'declared_value',
        'receiver_name',
        'receiver_phone',
        'receiver_city',
        'status',
        'decision_note',
        'accepted_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight_kg' => 'decimal:2',
        'declared_value' => 'integer',
        'status' => \App\Enums\RequestStatus::class,
        'accepted_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast (Laravel 11+ syntax support).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
            'declared_value' => 'integer',
            'status' => \App\Enums\RequestStatus::class,
            'accepted_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Relationship: The trip this shipment is requested for.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Relationship: The sender (User) who requested this shipment.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship: Status audit trail history for this shipment.
     */
    public function shipmentStatusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class);
    }

    /**
     * Alias for shipment status histories.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class);
    }

    /**
     * Relationship: Review left for this shipment.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Helper: Check if request is currently pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'en_attente'
            || (class_exists(RequestStatus::class) && $this->status === RequestStatus::EN_ATTENTE);
    }

    /**
     * Helper: Check if request has been accepted by the groupeur.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'acceptee'
            || (class_exists(RequestStatus::class) && $this->status === RequestStatus::ACCEPTEE);
    }

    /**
     * Helper: Check if shipment has been delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'livree'
            || (class_exists(RequestStatus::class) && $this->status === RequestStatus::LIVREE);
    }

    /**
     * Helper: Check if given user can leave a review on this shipment.
     * Can only leave review if status is 'livree', user is the sender, and no review exists yet.
     */
    public function canLeaveReview(User $user): bool
    {
        if ($this->status !== 'livree') {
            return false;
        }

        if ((int) $user->id !== (int) $this->sender_id) {
            return false;
        }

        return !$this->review()->where('author_id', $user->id)->exists();
    }

    /**
     * Generate WhatsApp click-to-chat link if status is accepted or beyond.
     * Allowed statuses: acceptee, collectee, en_transit, livree.
     *
     * @param string|null $recipient Target contact ('groupeur' | 'sender' | 'receiver')
     * @return string|null Full wa.me link with encoded message, or null if not eligible
     */
    public function whatsappLink(?string $recipient = 'groupeur'): ?string
    {
        $allowedStatuses = ['acceptee', 'collectee', 'en_transit', 'livree'];

        if (!in_array($this->status, $allowedStatuses, true)) {
            return null;
        }

        $phone = null;
        if ($recipient === 'sender') {
            $phone = $this->sender?->phone;
        } elseif ($recipient === 'receiver') {
            $phone = $this->receiver_phone;
        } else {
            // Default: groupeur's phone
            $phone = $this->trip?->groupeur?->phone;
        }

        if (!$phone) {
            return null;
        }

        // Keep only digits for international format (e.g., 221770000000)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($cleanPhone)) {
            return null;
        }

        $message = "Bonjour, concernant le colis #{$this->id} ({$this->category}, {$this->weight_kg} kg) sur GPsen.";
        $encodedMessage = rawurlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}
