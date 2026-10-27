<?php

namespace App\Models;

use App\Enums\TripStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'groupeur_id',
        'origin_country',
        'origin_city',
        'destination_country',
        'destination_city',
        'departure_date',
        'arrival_date',
        'capacity_kg',
        'price_per_kg',
        'accepted_items',
        'prohibited_items',
        'pickup_info',
        'status',
        'cancelled_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'arrival_date' => 'date',
            'capacity_kg' => 'decimal:2',
            'price_per_kg' => 'integer',
            'status' => \App\Enums\TripStatus::class,
        ];
    }

    /**
     * Relationship: The groupeur (User) who published this trip.
     */
    public function groupeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'groupeur_id');
    }

    /**
     * Relationship: Shipment requests submitted for this trip.
     */
    public function shipmentRequests(): HasMany
    {
        return $this->hasMany(ShipmentRequest::class);
    }

    /**
     * Scope: Filter trips that are open for bookings and departing today or in the future.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'ouvert')
                     ->whereDate('departure_date', '>=', Carbon::today());
    }

    /**
     * Scope: Search trips by origin city, destination city, and departure date.
     *
     * @param Builder $query
     * @param string|null $origin
     * @param string|null $destination
     * @param string|null $date
     * @return Builder
     */
    public function scopeSearch(
        Builder $query,
        ?string $origin = null,
        ?string $destination = null,
        ?string $date = null
    ): Builder {
        return $query
            ->when($origin, function (Builder $q, string $origin) {
                $q->where(function (Builder $sub) use ($origin) {
                    $sub->where('origin_city', 'like', "%{$origin}%")
                        ->orWhere('origin_country', 'like', "%{$origin}%");
                });
            })
            ->when($destination, function (Builder $q, string $destination) {
                $q->where(function (Builder $sub) use ($destination) {
                    $sub->where('destination_city', 'like', "%{$destination}%")
                        ->orWhere('destination_country', 'like', "%{$destination}%");
                });
            })
            ->when($date, function (Builder $q, string $date) {
                $q->whereDate('departure_date', '=', $date);
            });
    }

    /**
     * Helper: Check if trip status is open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'ouvert'
            || (class_exists(TripStatus::class) && $this->status === TripStatus::OUVERT);
    }

    /**
     * Helper: Check if trip is fully booked.
     */
    public function isFull(): bool
    {
        return $this->status === 'complet'
            || (class_exists(TripStatus::class) && $this->status === TripStatus::COMPLET);
    }

    /**
     * Helper: Check if trip is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'annule'
            || (class_exists(TripStatus::class) && $this->status === TripStatus::ANNULE);
    }

    /**
     * Helper: Calculate remaining available capacity in kilograms.
     */
    public function getRemainingCapacityAttribute(): float
    {
        $bookedKg = (float) $this->shipmentRequests()
            ->whereIn('status', ['acceptee', 'collectee', 'en_transit', 'livree'])
            ->sum('weight_kg');

        $remaining = (float) $this->capacity_kg - $bookedKg;

        return max(0.0, round($remaining, 2));
    }
}
