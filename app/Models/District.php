<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'province_id'
    ];

    // Relations
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function crmRecords(): HasMany
    {
        return $this->hasMany(CrmRecord::class);
    }

    // Scopes - Performans için
    public function scopeByProvince(Builder $query, int $provinceId): Builder
    {
        return $query->where('province_id', $provinceId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeWithProvince(Builder $query): Builder
    {
        return $query->with('province:id,name,plate_code');
    }

    // Helper Methods
    public static function getByProvinceId(int $provinceId)
    {
        return self::where('province_id', $provinceId)
                   ->orderBy('name')
                   ->get();
    }
}