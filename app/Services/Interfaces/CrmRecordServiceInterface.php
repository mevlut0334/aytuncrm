<?php

namespace App\Services\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\CrmRecord;

interface CrmRecordServiceInterface
{
    /**
     * Filtreleme ile liste getir
     */
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Tek bir firma detayını getir
     */
    public function show(int $id): ?CrmRecord;

    /**
     * Yeni firma kaydet
     */
    public function store(array $data): CrmRecord;

    /**
     * Firma güncelle
     */
    public function update(int $id, array $data): CrmRecord;

    /**
     * Firma sil
     */
    public function delete(int $id): bool;

    /**
     * Dosya numarasına göre firma bul
     */
    public function findByFileNumber(string $fileNumber): ?CrmRecord;

    /**
     * Vergi numarasına göre firma bul
     */
    public function findByTaxNumber(string $taxNumber): ?CrmRecord;

    /**
     * Aktif sözleşmeleri getir
     */
    public function getActiveContracts(): Collection;

    /**
     * Süresi dolmak üzere olan sözleşmeleri getir
     */
    public function getExpiringSoonContracts(int $days = 30): Collection;

    /**
     * Dashboard istatistikleri
     */
    public function getStats(): array;

    // ✅ Unique kontrol metodları kaldırıldı
    // isFileNumberUnique() ve isTaxNumberUnique() artık interface'de yok
}