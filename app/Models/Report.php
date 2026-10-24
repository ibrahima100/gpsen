<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reporter_id',
        'target_user_id',
        'trip_id',
        'shipment_request_id',
        'reason',
        'description',
        'status',
        'admin_note',
        'handled_by',
    ];

    /**
     * Relationship: The user who filed this report.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Relationship: The user being reported.
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Relationship: Optional trip associated with the report.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Relationship: Optional shipment request associated with the report.
     */
    public function shipmentRequest(): BelongsTo
    {
        return $this->belongsTo(ShipmentRequest::class);
    }

    /**
     * Relationship: The administrator who handled this report.
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Helper: Check if report is pending review.
     */
    public function isPending(): bool
    {
        return $this->status === 'en_attente';
    }

    /**
     * Helper: Check if report has been resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === 'traite';
    }

    /**
     * Helper: Check if report was dismissed without action.
     */
    public function isDismissed(): bool
    {
        return $this->status === 'rejete';
    }
}
