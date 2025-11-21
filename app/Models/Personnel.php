<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Personnel extends Model
{
    protected $fillable = [
        'name',
        'role',
        'phone',
        'email'
    ];

    // Scope'lar - performans için
    public function scopeDoctor($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopeHealthStaff($query)
    {
        return $query->where('role', 'health_staff');
    }

    public function scopeSafetyExpert($query)
    {
        return $query->where('role', 'safety_expert');
    }

    public function scopeAccountant($query)
    {
        return $query->where('role', 'accountant');
    }

    // Relations
    public function crmRecordsAsDoctor(): HasMany
    {
        return $this->hasMany(CrmRecord::class, 'doctor_id');
    }

    public function crmRecordsAsHealthStaff(): HasMany
    {
        return $this->hasMany(CrmRecord::class, 'health_staff_id');
    }

    public function crmRecordsAsSafetyExpert(): HasMany
    {
        return $this->hasMany(CrmRecord::class, 'safety_expert_id');
    }

    public function crmRecordsAsAccountant(): HasMany
    {
        return $this->hasMany(CrmRecord::class, 'accountant_id');
    }
}