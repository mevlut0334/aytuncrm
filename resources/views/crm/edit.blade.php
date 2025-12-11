@extends('layouts.app')

@section('title', 'Firma Düzenle - İSG CRM')

@push('styles')
<style>
    /* Başlık responsive */
    @media (max-width: 575.98px) {
        h2 {
            font-size: 1.3rem;
        }
        .text-muted.small {
            font-size: 0.75rem;
        }
    }

    /* Kart header responsive */
    @media (max-width: 575.98px) {
        .card-header {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
    }

    /* Form label responsive */
    @media (max-width: 575.98px) {
        .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
    }

    /* Buton grubu responsive */
    @media (max-width: 575.98px) {
        .btn-group-responsive {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }
        .btn-group-responsive .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Başlık --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4 gap-2">
        <div>
            <h2 class="mb-0"><i class="bi bi-building-gear"></i> Firma Düzenle</h2>
            <small class="text-muted d-block mt-1">{{ $record->company_title }}</small>
        </div>
        <a href="{{ route('crm.show', $record->id) }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Geri Dön
        </a>
    </div>

    {{-- Hata Mesajları --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle"></i> Dikkat!</strong> Lütfen aşağıdaki hataları düzeltin:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('crm.update', $record->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- 1. FİRMA BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-building"></i> Firma Bilgileri
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="file_number" class="form-label">
                            Dosya Numarası <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm @error('file_number') is-invalid @enderror"
                               id="file_number" name="file_number"
                               value="{{ old('file_number', $record->file_number) }}"
                               placeholder="Örn: 2024-001" required>
                        @error('file_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-9">
                        <label for="company_title" class="form-label">
                            Firma Unvanı <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm @error('company_title') is-invalid @enderror"
                               id="company_title" name="company_title"
                               value="{{ old('company_title', $record->company_title) }}"
                               placeholder="Firma tam unvanını giriniz" required>
                        @error('company_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="company_address" class="form-label">
                            Firma Adresi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm @error('company_address') is-invalid @enderror"
                                  id="company_address" name="company_address" rows="2"
                                  placeholder="Firma adresini giriniz" required>{{ old('company_address', $record->company_address) }}</textarea>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- ✅ YENİ ALAN: Hizmet Adresi --}}
            <div class="col-12">
                <label for="service_address" class="form-label">
                    Hizmet Adresi
                </label>
                <textarea class="form-control form-control-sm @error('service_address') is-invalid @enderror"
                          id="service_address" name="service_address" rows="2"
                          placeholder="Hizmet adresi giriniz (opsiyonel)">{{ old('service_address', $record->service_address) }}</textarea>
                @error('service_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                </div>
            </div>
        </div>

        {{-- 2. LOKASYON BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-geo-alt"></i> Lokasyon Bilgileri
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-4">
                        <label for="province_id" class="form-label">
                            İl <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm @error('province_id') is-invalid @enderror"
                                id="province_id" name="province_id" required>
                            <option value="">Seçiniz</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}"
                                    {{ old('province_id', $record->province_id) == $province->id ? 'selected' : '' }}>
                                    {{ $province->plate_code }} - {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('province_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="district_id" class="form-label">
                            İlçe <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm @error('district_id') is-invalid @enderror"
                                id="district_id" name="district_id" required>
                            <option value="">Önce il seçiniz</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ old('district_id', $record->district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="neighborhood" class="form-label">Mahalle</label>
                        <input type="text" class="form-control form-control-sm @error('neighborhood') is-invalid @enderror"
                               id="neighborhood" name="neighborhood"
                               value="{{ old('neighborhood', $record->neighborhood) }}"
                               placeholder="Mahalle adı">
                        @error('neighborhood')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. VERGİ VE RESMİ BİLGİLER --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-file-earmark-text"></i> Vergi ve Resmi Bilgiler
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tax_office" class="form-label">
                            Vergi Dairesi <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm @error('tax_office') is-invalid @enderror"
                               id="tax_office" name="tax_office"
                               value="{{ old('tax_office', $record->tax_office) }}"
                               placeholder="Örn: Çankaya" required>
                        @error('tax_office')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tax_number" class="form-label">
                            Vergi Numarası <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm @error('tax_number') is-invalid @enderror"
                               id="tax_number" name="tax_number"
                               value="{{ old('tax_number', $record->tax_number) }}"
                               placeholder="10 haneli" maxlength="10" required>
                        @error('tax_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="sgk_number" class="form-label">SGK Numarası</label>
                        <input type="text" class="form-control form-control-sm @error('sgk_number') is-invalid @enderror"
                               id="sgk_number" name="sgk_number"
                               value="{{ old('sgk_number', $record->sgk_number) }}"
                               placeholder="SGK sicil no">
                        @error('sgk_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="trade_register_no" class="form-label">Ticaret Sicil No</label>
                        <input type="text" class="form-control form-control-sm @error('trade_register_no') is-invalid @enderror"
                               id="trade_register_no" name="trade_register_no"
                               value="{{ old('trade_register_no', $record->trade_register_no) }}"
                               placeholder="Ticaret sicil">
                        @error('trade_register_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="identity_no" class="form-label">TC Kimlik No</label>
                        <input type="text" class="form-control form-control-sm @error('identity_no') is-invalid @enderror"
                               id="identity_no" name="identity_no"
                               value="{{ old('identity_no', $record->identity_no) }}"
                               placeholder="11 haneli" maxlength="11">
                        @error('identity_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. YETKİLİ BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-person-badge"></i> Yetkili Bilgileri
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-4">
                        <label for="officer_name" class="form-label">
                            Yetkili Adı Soyadı
                        </label>
                        <input type="text" class="form-control form-control-sm @error('officer_name') is-invalid @enderror"
                               id="officer_name" name="officer_name"
                               value="{{ old('officer_name', $record->officer_name) }}"
                               placeholder="Ad Soyad">
                        @error('officer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="phone" class="form-label">
                            Telefon
                        </label>
                        <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                               id="phone" name="phone"
                               value="{{ old('phone', $record->phone) }}"
                               placeholder="0XXX XXX XX XX">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                               id="email" name="email"
                               value="{{ old('email', $record->email) }}"
                               placeholder="ornek@firma.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. İŞ YERİ BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle"></i> İş Yeri Bilgileri
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-6">
                        <label for="personnel_count" class="form-label">
                            Personel Sayısı
                        </label>
                        <input type="number" class="form-control form-control-sm @error('personnel_count') is-invalid @enderror"
                               id="personnel_count" name="personnel_count"
                               value="{{ old('personnel_count', $record->personnel_count) }}"
                               min="0" placeholder="Örn: 50">
                        @error('personnel_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="danger_level_id" class="form-label">
                            Tehlike Sınıfı
                        </label>
                        <select class="form-select form-select-sm @error('danger_level_id') is-invalid @enderror"
                                id="danger_level_id" name="danger_level_id">
                            <option value="">Seçiniz</option>
                            @foreach($dangerLevels as $level)
                                <option value="{{ $level->id }}"
                                    {{ old('danger_level_id', $record->danger_level_id) == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('danger_level_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. PERSONEL ATAMALARI --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-success text-white">
                <i class="bi bi-people"></i> Personel Atamaları
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-6">
                        <label for="doctor_name" class="form-label">İş Yeri Hekimi</label>
                        <input type="text" class="form-control form-control-sm @error('doctor_name') is-invalid @enderror"
                               id="doctor_name" name="doctor_name"
                               value="{{ old('doctor_name', $record->doctor_name) }}"
                               placeholder="Hekim adı soyadı">
                        @error('doctor_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="health_staff_name" class="form-label">Sağlık Personeli</label>
                        <input type="text" class="form-control form-control-sm @error('health_staff_name') is-invalid @enderror"
                               id="health_staff_name" name="health_staff_name"
                               value="{{ old('health_staff_name', $record->health_staff_name) }}"
                               placeholder="Sağlık personeli adı soyadı">
                        @error('health_staff_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="safety_expert_name" class="form-label">İş Güvenliği Uzmanı</label>
                        <input type="text" class="form-control form-control-sm @error('safety_expert_name') is-invalid @enderror"
                               id="safety_expert_name" name="safety_expert_name"
                               value="{{ old('safety_expert_name', $record->safety_expert_name) }}"
                               placeholder="İş güvenliği uzmanı adı soyadı">
                        @error('safety_expert_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="accountant_name" class="form-label">Mali Müşavir</label>
                        <input type="text" class="form-control form-control-sm @error('accountant_name') is-invalid @enderror"
                               id="accountant_name" name="accountant_name"
                               value="{{ old('accountant_name', $record->accountant_name) }}"
                               placeholder="Mali müşavir adı soyadı">
                        @error('accountant_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 7. SÖZLEŞME BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-file-text"></i> Sözleşme Bilgileri
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="contract_creator_name" class="form-label">
                            Sözleşmeyi Yapan
                        </label>
                        <input type="text" class="form-control form-control-sm @error('contract_creator_name') is-invalid @enderror"
                               id="contract_creator_name" name="contract_creator_name"
                               value="{{ old('contract_creator_name', $record->contract_creator_name) }}"
                               placeholder="Ad Soyad">
                        @error('contract_creator_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="contract_start" class="form-label">
                            Sözleşme Başlangıç
                        </label>
                        <input type="date" class="form-control form-control-sm @error('contract_start') is-invalid @enderror"
                               id="contract_start" name="contract_start"
                               value="{{ old('contract_start', $record->contract_start ? $record->contract_start->format('Y-m-d') : '') }}"
                               >
                        @error('contract_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="contract_end" class="form-label">
                            Sözleşme Bitiş
                        </label>
                        <input type="date" class="form-control form-control-sm @error('contract_end') is-invalid @enderror"
                               id="contract_end" name="contract_end"
                               value="{{ old('contract_end', $record->contract_end ? $record->contract_end->format('Y-m-d') : '') }}"
                               >
                        @error('contract_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="contract_months" class="form-label">
                            Sözleşme Süresi (Ay)
                        </label>
                        <input type="number" class="form-control form-control-sm @error('contract_months') is-invalid @enderror"
                               id="contract_months" name="contract_months"
                               value="{{ old('contract_months', $record->contract_months) }}"
                               min="1" max="120">
                        @error('contract_months')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 8. FİYAT BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-currency-exchange"></i> Fiyat Bilgileri
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-4">
                        <label for="monthly_price" class="form-label">
                            Aylık Fiyat (₺)
                        </label>
                        <input type="number" class="form-control form-control-sm @error('monthly_price') is-invalid @enderror"
                               id="monthly_price" name="monthly_price"
                               value="{{ old('monthly_price', $record->monthly_price) }}"
                               step="0.01" min="0" placeholder="0.00">
                        @error('monthly_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="monthly_kdv" class="form-label">
                            KDV (₺)
                        </label>
                        <input type="number" class="form-control form-control-sm @error('monthly_kdv') is-invalid @enderror"
                               id="monthly_kdv" name="monthly_kdv"
                               value="{{ old('monthly_kdv', $record->monthly_kdv) }}"
                               step="0.01" min="0" placeholder="0.00">
                        @error('monthly_kdv')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="monthly_total" class="form-label">
                            Toplam (₺)
                        </label>
                        <input type="number" class="form-control form-control-sm @error('monthly_total') is-invalid @enderror"
                               id="monthly_total" name="monthly_total"
                               value="{{ old('monthly_total', $record->monthly_total) }}"
                               step="0.01" min="0" placeholder="0.00">
                        @error('monthly_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 9. RANDEVU BİLGİLERİ --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-calendar-check"></i> Randevu Bilgileri (Opsiyonel)
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-6">
                        <label for="appointment_date" class="form-label">Randevu Tarihi</label>
                        <input type="date" class="form-control form-control-sm @error('appointment_date') is-invalid @enderror"
                               id="appointment_date" name="appointment_date"
                               value="{{ old('appointment_date', $record->appointment_date ? $record->appointment_date->format('Y-m-d') : '') }}">
                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="appointment_time" class="form-label">Randevu Saati</label>
                        <input type="time" class="form-control form-control-sm @error('appointment_time') is-invalid @enderror"
                               id="appointment_time" name="appointment_time"
                               value="{{ old('appointment_time', $record->appointment_time) }}">
                        @error('appointment_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 10. NOTLAR --}}
        <div class="card mb-3 mb-md-4">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-sticky"></i> Notlar
            </div>
            <div class="card-body">
                <textarea class="form-control form-control-sm @error('notes') is-invalid @enderror"
                          id="notes" name="notes" rows="3"
                          placeholder="Firma hakkında ek notlar...">{{ old('notes', $record->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- BUTONLAR --}}
        <div class="btn-group-responsive d-flex justify-content-end gap-2 mb-4 mb-md-5">
            <a href="{{ route('crm.show', $record->id) }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> İptal
            </a>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-save"></i> Değişiklikleri Kaydet
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');
    const oldDistrictId = '{{ old("district_id", $record->district_id) }}';

    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        districtSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        if (!provinceId) {
            districtSelect.innerHTML = '<option value="">Önce il seçiniz</option>';
            return;
        }

        fetch(`/api/districts/${provinceId}`)
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Seçiniz</option>';
                data.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    if (oldDistrictId && district.id == oldDistrictId) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                districtSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    });

    if (provinceSelect.value) {
        provinceSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
