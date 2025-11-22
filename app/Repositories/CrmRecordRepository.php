<?php

namespace App\Repositories;

use App\Models\CrmRecord;
use App\Repositories\Interfaces\CrmRecordRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CrmRecordRepository implements CrmRecordRepositoryInterface
{
    public function __construct(
        protected CrmRecord $model
    ) {}

    /**
     * Filtreleme ile liste getir (PERFORMANS ODAKLI + 8 FİLTRE)
     */
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select([
                'id',
                'file_number',
                'company_title',
                'company_address',
                'province_id',
                'district_id',
                'danger_level_id',
                'officer_name',
                'phone',
                'email',
                'tax_number',
                'personnel_count',
                'doctor_name',
                'health_staff_name',
                'safety_expert_name',
                'accountant_name',
                'doctor_id',
                'health_staff_id',
                'safety_expert_id',
                'accountant_id',
                'contract_creator_id',
                'contract_creator_name',
                'contract_start',
                'contract_end',
                'created_at'
            ])
            ->with([
                'province:id,name,plate_code',
                'district:id,name',
                'dangerLevel:id,name',
                'doctor:id,name',
                'healthStaff:id,name',
                'safetyExpert:id,name',
                'accountant:id,name',
                'contractCreator:id,name'
            ]);

        // ✅ FİLTRE 1: Dosya Numarası
        if (!empty($filters['file_number'])) {
            $query->where('file_number', 'like', '%' . $filters['file_number'] . '%');
        }

        // ✅ FİLTRE 2: Firma Unvanı
        if (!empty($filters['company_title'])) {
            $query->where('company_title', 'like', '%' . $filters['company_title'] . '%');
        }

        // ✅ FİLTRE 3: Tehlike Sınıfı (dangerLevel tablosunda arama)
        if (!empty($filters['danger_level'])) {
            $query->whereHas('dangerLevel', function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['danger_level'] . '%');
            });
        }

        // ✅ FİLTRE 4: İş Yeri Hekimi (manuel giriş alanı)
        if (!empty($filters['doctor_name'])) {
            $query->where('doctor_name', 'like', '%' . $filters['doctor_name'] . '%');
        }

        // ✅ FİLTRE 5: Sağlık Personeli (manuel giriş alanı)
        if (!empty($filters['health_staff_name'])) {
            $query->where('health_staff_name', 'like', '%' . $filters['health_staff_name'] . '%');
        }

        // ✅ FİLTRE 6: İş Güvenliği Uzmanı (manuel giriş alanı)
        if (!empty($filters['safety_expert_name'])) {
            $query->where('safety_expert_name', 'like', '%' . $filters['safety_expert_name'] . '%');
        }

        // ✅ FİLTRE 7: Mali Müşavir (manuel giriş alanı)
        if (!empty($filters['accountant_name'])) {
            $query->where('accountant_name', 'like', '%' . $filters['accountant_name'] . '%');
        }

        // ✅ FİLTRE 8: Sözleşmeyi Yapan (contractCreator tablosunda arama)
       // ✅ FİLTRE 8: Sözleşmeyi Yapan (contract_creator_name alanında arama)
        if (!empty($filters['contract_creator'])) {
           $query->where('contract_creator_name', 'like', '%' . $filters['contract_creator'] . '%');
        }

        // Eski dropdown filtreleri (opsiyonel - gerekirse kullanılabilir)
        if (!empty($filters['danger_level_id'])) {
            $query->where('danger_level_id', $filters['danger_level_id']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['health_staff_id'])) {
            $query->where('health_staff_id', $filters['health_staff_id']);
        }

        if (!empty($filters['safety_expert_id'])) {
            $query->where('safety_expert_id', $filters['safety_expert_id']);
        }

        if (!empty($filters['accountant_id'])) {
            $query->where('accountant_id', $filters['accountant_id']);
        }

        if (!empty($filters['contract_creator_id'])) {
            $query->where('contract_creator_id', $filters['contract_creator_id']);
        }

        // Sıralama
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Tekil kayıt getir (tüm ilişkiler ile)
     */
    public function show(int $id): ?CrmRecord
    {
        return $this->model->withAllRelations()->findOrFail($id);
    }

    /**
     * Yeni kayıt oluştur
     */
    public function store(array $data): CrmRecord
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * Kayıt güncelle
     */
    public function update(int $id, array $data): CrmRecord
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->model->findOrFail($id);
            $record->update($data);
            return $record->fresh();
        });
    }

    /**
     * Kayıt sil
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->model->findOrFail($id);
            return $record->delete();
        });
    }

    /**
     * Dosya numarasına göre bul
     */
    public function findByFileNumber(string $fileNumber): ?CrmRecord
    {
        return $this->model->where('file_number', $fileNumber)->first();
    }

    /**
     * Vergi numarasına göre bul
     */
    public function findByTaxNumber(string $taxNumber): ?CrmRecord
    {
        return $this->model->where('tax_number', $taxNumber)->first();
    }

    /**
     * Aktif sözleşmeleri getir
     */
    public function getActiveContracts(): Collection
    {
        return $this->model->activeContracts()
            ->select(['id', 'file_number', 'company_title', 'contract_end'])
            ->get();
    }

    /**
     * Süresi dolmak üzere olan sözleşmeler
     */
    public function getExpiringSoonContracts(int $days = 30): Collection
    {
        return $this->model
            ->whereBetween('contract_end', [now(), now()->addDays($days)])
            ->select(['id', 'file_number', 'company_title', 'contract_end', 'phone', 'email'])
            ->with('contractCreator:id,name')
            ->orderBy('contract_end')
            ->get();
    }

    /**
     * Belirli personele atanmış firmalar
     */
    public function getByPersonnel(int $personnelId, string $role): Collection
    {
        $column = match($role) {
            'doctor' => 'doctor_id',
            'health_staff' => 'health_staff_id',
            'safety_expert' => 'safety_expert_id',
            'accountant' => 'accountant_id',
            default => throw new \InvalidArgumentException("Invalid role: {$role}")
        };

        return $this->model
            ->where($column, $personnelId)
            ->select(['id', 'file_number', 'company_title', 'phone', 'email'])
            ->get();
    }

    /**
     * Dashboard için istatistikler
     */
    public function getStats(): array
    {
        return [
            'total_companies' => $this->model->count(),
            'active_contracts' => $this->model->activeContracts()->count(),
            'expired_contracts' => $this->model->expiredContracts()->count(),
            'expiring_soon' => $this->model
                ->whereBetween('contract_end', [now(), now()->addDays(30)])
                ->count(),
            'by_danger_level' => $this->model
                ->select('danger_level_id', DB::raw('count(*) as count'))
                ->groupBy('danger_level_id')
                ->with('dangerLevel:id,name')
                ->get()
                ->pluck('count', 'dangerLevel.name'),
        ];
    }
}