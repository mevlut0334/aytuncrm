<?php

namespace App\Services;

use App\Models\CrmRecord;
use App\Repositories\Interfaces\CrmRecordRepositoryInterface;
use App\Services\Interfaces\CrmRecordServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class CrmRecordService implements CrmRecordServiceInterface
{
    public function __construct(
        protected CrmRecordRepositoryInterface $repository
    ) {}

    /**
     * Filtreleme ile liste
     */
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->repository->index($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('CrmRecordService::index Error', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Firma detayı
     */
    public function show(int $id): ?CrmRecord
    {
        try {
            return $this->repository->show($id);
        } catch (\Exception $e) {
            Log::error('CrmRecordService::show Error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Yeni firma kaydet
     */
    public function store(array $data): CrmRecord
    {
        try {
            // ✅ Unique kontrolleri kaldırıldı - aynı firma için birden fazla kayıt oluşturulabilir

            // Sözleşme bitiş tarihi hesapla (eğer boşsa)
            if (isset($data['contract_start']) && empty($data['contract_end'])) {
                $months = $data['contract_months'] ?? 12;
                $data['contract_end'] = date('Y-m-d', strtotime($data['contract_start'] . " +{$months} months"));
            }

            // KDV ve Toplam hesapla (eğer boşsa)
            if (isset($data['monthly_price'])) {
                if (empty($data['monthly_kdv'])) {
                    $data['monthly_kdv'] = $data['monthly_price'] * 0.20; // %20 KDV
                }
                if (empty($data['monthly_total'])) {
                    $data['monthly_total'] = $data['monthly_price'] + $data['monthly_kdv'];
                }
            }

            return $this->repository->store($data);
        } catch (\Exception $e) {
            Log::error('CrmRecordService::store Error', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Firma güncelle
     */
    public function update(int $id, array $data): CrmRecord
    {
        try {
            // ✅ Unique kontrolleri kaldırıldı - aynı firma için birden fazla kayıt oluşturulabilir

            // Sözleşme bitiş tarihi hesapla (eğer değiştiyse)
            if (isset($data['contract_start']) && isset($data['contract_months'])) {
                $data['contract_end'] = date('Y-m-d', strtotime($data['contract_start'] . " +{$data['contract_months']} months"));
            }

            // KDV ve Toplam hesapla
            if (isset($data['monthly_price'])) {
                $data['monthly_kdv'] = $data['monthly_price'] * 0.20;
                $data['monthly_total'] = $data['monthly_price'] + $data['monthly_kdv'];
            }

            return $this->repository->update($id, $data);
        } catch (\Exception $e) {
            Log::error('CrmRecordService::update Error', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Firma sil
     */
    public function delete(int $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('CrmRecordService::delete Error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Dosya numarasına göre bul
     */
    public function findByFileNumber(string $fileNumber): ?CrmRecord
    {
        return $this->repository->findByFileNumber($fileNumber);
    }

    /**
     * Vergi numarasına göre bul
     */
    public function findByTaxNumber(string $taxNumber): ?CrmRecord
    {
        return $this->repository->findByTaxNumber($taxNumber);
    }

    /**
     * Aktif sözleşmeler
     */
    public function getActiveContracts(): Collection
    {
        return $this->repository->getActiveContracts();
    }

    /**
     * Süresi dolmak üzere sözleşmeler
     */
    public function getExpiringSoonContracts(int $days = 30): Collection
    {
        return $this->repository->getExpiringSoonContracts($days);
    }

    /**
     * İstatistikler
     */
    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    // ✅ Unique kontrol metodları kaldırıldı
    // isFileNumberUnique() ve isTaxNumberUnique() metodları artık kullanılmıyor
}