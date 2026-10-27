<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'groupeur_profile_id',
        'document_path',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(GroupeurProfile::class, 'groupeur_profile_id');
    }
}
