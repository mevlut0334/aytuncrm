<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DangerLevel extends Model
{
    protected $fillable = ['name'];

    // Relations
    public function crmRecords(): HasMany
    {
        return $this->hasMany(CrmRecord::class);
    }

    // Helper methods
    public static function getByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }
}