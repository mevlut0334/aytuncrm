<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CrmRecord extends Model
{
    protected $fillable = [
         'file_number',
    'company_title',
    'company_address',
    'service_address',
    'province_id',
    'district_id',
    'neighborhood',
    'tax_office',
    'tax_number',
    'sgk_number',
    'trade_register_no',
    'identity_no',
    'officer_name',
    'phone',
    'email',
    'personnel_count',
    'danger_level_id',
    'doctor_id',
    'health_staff_id',
    'safety_expert_id',
    'accountant_id',
    'doctor_name',
    'health_staff_name',
    'safety_expert_name',
    'accountant_name',
    'contract_creator_id',
    'contract_creator_name',
    'contract_start',
    'contract_end',
    'contract_months',
    'monthly_price',
    'monthly_kdv',
    'monthly_total',
    'appointment_date',
    'appointment_time',
    'notes'
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end' => 'date',
        'appointment_date' => 'date',
        'appointment_time' => 'string',
        'monthly_price' => 'decimal:2',
        'monthly_kdv' => 'decimal:2',
        'monthly_total' => 'decimal:2',
        'personnel_count' => 'integer',
        'contract_months' => 'integer'
    ];

    // Relations - Eager loading için optimize edilmiş
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function dangerLevel(): BelongsTo
    {
        return $this->belongsTo(DangerLevel::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'doctor_id');
    }

    public function healthStaff(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'health_staff_id');
    }

    public function safetyExpert(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'safety_expert_id');
    }

    public function accountant(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'accountant_id');
    }

    public function contractCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contract_creator_id');
    }

    // Scopes - Performans için
    public function scopeWithAllRelations(Builder $query): Builder
    {
        return $query->with([
            'province:id,name,plate_code',
            'district:id,province_id,name',
            'dangerLevel:id,name',
            'doctor:id,name,role,phone',
            'healthStaff:id,name,role,phone',
            'safetyExpert:id,name,role,phone',
            'accountant:id,name,role,phone',
            'contractCreator:id,name,email'
        ]);
    }

    public function scopeActiveContracts(Builder $query): Builder
    {
        return $query->where('contract_end', '>=', now());
    }

    public function scopeExpiredContracts(Builder $query): Builder
    {
        return $query->where('contract_end', '<', now());
    }

    public function scopeUpcomingAppointments(Builder $query): Builder
    {
        return $query->where('appointment_date', '>=', now())
                     ->orderBy('appointment_date');
    }

    public function scopeByProvince(Builder $query, int $provinceId): Builder
    {
        return $query->where('province_id', $provinceId);
    }

    public function scopeByDangerLevel(Builder $query, int $dangerLevelId): Builder
    {
        return $query->where('danger_level_id', $dangerLevelId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('company_title', 'like', "%{$search}%")
              ->orWhere('file_number', 'like', "%{$search}%")
              ->orWhere('tax_number', 'like', "%{$search}%")
              ->orWhere('officer_name', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function isContractActive(): bool
    {
        return $this->contract_end >= now();
    }

    public function getDaysUntilContractEnd(): int
    {
        return now()->diffInDays($this->contract_end, false);
    }

    public function getContractStatus(): string
    {
        $days = $this->getDaysUntilContractEnd();

        if ($days < 0) return 'expired';
        if ($days <= 30) return 'expiring_soon';

        return 'active';
    }
}
