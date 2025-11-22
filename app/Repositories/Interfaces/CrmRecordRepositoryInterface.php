<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\CrmRecord;

interface CrmRecordRepositoryInterface
{
    /**
     * Tüm kayıtları filtrelerle birlikte getir (paginated)
     */
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Tek bir kaydı ID ile getir (relations ile)
     */
    public function show(int $id): ?CrmRecord;

    /**
     * Yeni kayıt oluştur
     */
    public function store(array $data): CrmRecord;

    /**
     * Kayıt güncelle
     */
    public function update(int $id, array $data): CrmRecord;

    /**
     * Kayıt sil
     */
    public function delete(int $id): bool;

    /**
     * Dosya numarasına göre kayıt bul
     */
    public function findByFileNumber(string $fileNumber): ?CrmRecord;

    /**
     * Vergi numarasına göre kayıt bul
     */
    public function findByTaxNumber(string $taxNumber): ?CrmRecord;

    /**
     * Aktif sözleşmeleri getir
     */
    public function getActiveContracts(): Collection;

    /**
     * Süresi dolmak üzere olan sözleşmeleri getir (30 gün içinde)
     */
    public function getExpiringSoonContracts(int $days = 30): Collection;

    /**
     * Belirli bir personele atanmış firmaları getir
     */
    public function getByPersonnel(int $personnelId, string $role): Collection;

    /**
     * İstatistikler için özet bilgi
     */
    public function getStats(): array;
}