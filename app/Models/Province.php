<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'plate_code',
        'region',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Relations
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function crmRecords(): HasMany
    {
        return $this->hasMany(CrmRecord::class, 'province_id');
    }

    // Scopes - Performans için
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeByRegion(Builder $query, string $region): Builder
    {
        return $query->where('region', $region);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('plate_code', 'like', "%{$search}%");
        });
    }

    public function scopeWithDistricts(Builder $query): Builder
    {
        return $query->with('districts:id,province_id,name');
    }

    // Helper Methods
    public static function getByPlateCode(string $plateCode): ?self
    {
        return self::where('plate_code', $plateCode)->first();
    }

    public static function getRegions(): array
    {
        return self::distinct()->pluck('region')->toArray();
    }
}