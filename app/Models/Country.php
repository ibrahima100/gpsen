<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'normalized_name',
    ];

    public static function normalizeString(string $value): string
    {
        return Str::lower(Str::ascii(trim($value)));
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
