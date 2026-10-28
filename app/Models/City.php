<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'normalized_name',
    ];

    public static function normalizeString(string $value): string
    {
        return Str::lower(Str::ascii(trim($value)));
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
