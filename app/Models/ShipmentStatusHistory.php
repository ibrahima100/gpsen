<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStatusHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipment_status_histories';

    /**
     * Indicates if the model should be timestamped.
     * This table is append-only and does not have an updated_at column.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string>
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'shipment_request_id',
        'status',
        'changed_by',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Boot the model and automatically fill created_at when creating.
     */
    protected static function booted(): void
    {
        static::creating(function (ShipmentStatusHistory $model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    /**
     * Relationship: The shipment request this history entry belongs to.
     */
    public function shipmentRequest(): BelongsTo
    {
        return $this->belongsTo(ShipmentRequest::class);
    }

    /**
     * Relationship: The user who performed or triggered this status change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Alias for author of the status change.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
