<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Satu baris data loker (lowongan) yang diinput admin.
 *
 * Urutan kolom mengikuti daftar di SPEC.json (`data.entities.jobs`).
 */
#[Fillable([
    'company_name',
    'address',
    'division',
    'city',
    'description',
    'requirements',
    'deadline',
    'latitude',
    'longitude',
    'is_active',
    'posted_by',
])]
class Job extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Admin yang mengunggah loker ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Pengguna yang menyimpan loker ini sebagai favorit.
     *
     * @return BelongsToMany<User, $this>
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'job_id', 'user_id')->withTimestamps();
    }
}
