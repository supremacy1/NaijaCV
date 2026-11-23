<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cv extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'full_name',
        'professional_title',
        'email',
        'phone',
        'address',
        'summary',
        'template',
        'is_paid',
        'guest_token',
        'expires_at',
        'payment_status',
        'payment_reference',
        'payment_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'expires_at' => 'datetime',
            'payment_verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }
}
