@extends('layouts.app')

@section('title', $record->company_title . ' - İSG CRM')

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

    /* Label ve değerler responsive */
    @media (max-width: 575.98px) {
        .form-label.text-muted.small {
            font-size: 0.75rem;
            margin-bottom: 0.1rem;
        }
        .fw-bold {
            font-size: 0.9rem;
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

    /* Badge responsive */
    @media (max-width: 575.98px) {
        .badge.fs-6 {
            font-size: 0.8rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Başlık --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4 gap-2">
        <div class="w-100 w-sm-auto">
            <h2 class="mb-0"><i class="bi bi-building"></i> {{ $record->company_title }}</h2>
            <small class="text-muted d-block mt-1">Dosya No: {{ $record->file_number }}</small>
        </div>
        <div class="btn-group-responsive d-flex gap-2 w-100 w-sm-auto">
            <a href="{{ route('crm.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Geri Dön
            </a>
            {{-- Düzenle ve Sil - Sadece Admin --}}
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('crm.edit', $record->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Düzenle
                </a>
                <form action="{{ route('crm.destroy', $record->id) }}" 
                      method="POST" 
                      class="d-inline w-100 w-sm-auto"
                      onsubmit="return confirm('Bu firmayı silmek istediğinizden emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100 w-sm-auto">
                        <i class="bi bi-trash"></i> Sil
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- MOBİL: Önce Özet Bilgiler (Sağ Kolon) --}}
        <div class="col-12 col-lg-4 order-1 order-lg-2 mb-3 mb-lg-0">
            {{-- Özet Bilgiler --}}
            <div class="card mb-3 mb-md-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-info-circle"></i> Özet Bilgiler
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-building text-primary"></i>
                            <strong>Dosya No:</strong> {{ $record->file_number }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-geo-alt text-danger"></i>
                            <strong>Lokasyon:</strong> 
                            {{ $record->province->name ?? '-' }}
                            @if($record->district)
                                / {{ $record->district->name }}
                            @endif
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-people text-info"></i>
                            <strong>Personel:</strong> {{ $record->personnel_count ?? '-' }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-shield text-warning"></i>
                            <strong>Tehlike:</strong> {{ $record->dangerLevel->name ?? '-' }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-calendar-range text-success"></i>
                            <strong>Sözleşme:</strong> {{ $record->contract_months ? $record->contract_months . ' Ay' : '-' }}
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Kayıt Bilgileri --}}
            <div class="card mb-3 mb-md-4">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-clock-history"></i> Kayıt Bilgileri
                </div>
                <div class="card-body">
                    <div class="mb-2 small">
                        <label class="form-label text-muted small mb-1">Oluşturan</label>
                        <p class="fw-bold mb-0">
                            @if($record->contract_creator_name)
                                <i class="bi bi-person-check text-success"></i> {{ $record->contract_creator_name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-2 small">
                        <label class="form-label text-muted small mb-1">Oluşturulma Tarihi</label>
                        <p class="fw-bold mb-0">
                            <i class="bi bi-calendar-plus"></i> {{ $record->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                    <div class="small">
                        <label class="form-label text-muted small mb-1">Son Güncelleme</label>
                        <p class="fw-bold mb-0">
                            <i class="bi bi-calendar-check"></i> {{ $record->updated_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sol Kolon - Detay Bilgileri --}}
        <div class="col-12 col-lg-8 order-2 order-lg-1">
            {{-- 1. FİRMA BİLGİLERİ --}}
            <div class="card mb-3 mb-md-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-building"></i> Firma Bilgileri
                </div>
                <div class="card-body">
                    <div class="row g-2 g-md-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="form-label text-muted small">Dosya Numarası</label>
                            <p class="fw-bold mb-0">{{ $record->file_number }}</p>
                        </div>
                        <div class="col-12 col-sm-6 col-md-8">
                            <label class="form-label text-muted small">Firma Unvanı</label>
                            <p class="fw-bold mb-0">{{ $record->company_title }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Firma Adresi</label>
                            <p class="mb-0">{{ $record->company_address ?? '-' }}</p>
                        </div>
                         {{-- ✅ YENİ ALAN: Hizmet Adresi --}}
            
            <div class="col-12">
                <strong>Hizmet Adresi:</strong>
                <p class="mb-0">{{ $record->service_address }}</p>
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
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">İl</label>
                            <p class="fw-bold mb-0">
                                @if($record->province)
                                    {{ $record->province->plate_code }} - {{ $record->province->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">İlçe</label>
                            <p class="fw-bold mb-0">
                                @if($record->district)
                                    {{ $record->district->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">Mahalle</label>
                            <p class="fw-bold mb-0">{{ $record->neighborhood ?? '-' }}</p>
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
                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="form-label text-muted small">Vergi Dairesi</label>
                            <p class="fw-bold mb-0">{{ $record->tax_office ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="form-label text-muted small">Vergi Numarası</label>
                            <p class="fw-bold mb-0">{{ $record->tax_number ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="form-label text-muted small">SGK Numarası</label>
                            <p class="fw-bold mb-0">{{ $record->sgk_number ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Ticaret Sicil No</label>
                            <p class="fw-bold mb-0">{{ $record->trade_register_no ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">TC Kimlik No</label>
                            <p class="fw-bold mb-0">{{ $record->identity_no ?? '-' }}</p>
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
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">Yetkili Adı Soyadı</label>
                            <p class="fw-bold mb-0">{{ $record->officer_name ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">Telefon</label>
                            <p class="fw-bold mb-0">
                                @if($record->phone)
                                    <a href="tel:{{ $record->phone }}" class="text-decoration-none">
                                        <i class="bi bi-telephone-fill text-success"></i> {{ $record->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">E-posta</label>
                            <p class="fw-bold mb-0">
                                @if($record->email)
                                    <a href="mailto:{{ $record->email }}" class="text-decoration-none">
                                        <i class="bi bi-envelope-fill text-primary"></i> 
                                        <span class="d-none d-md-inline">{{ $record->email }}</span>
                                        <span class="d-inline d-md-none">E-posta</span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Personel Sayısı</label>
                            <p class="fw-bold mb-0">{{ $record->personnel_count ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Tehlike Sınıfı</label>
                            <p class="mb-0">
                                @if($record->dangerLevel)
                                    <span class="badge bg-{{ $record->dangerLevel->name == 'Az Tehlikeli' ? 'success' : ($record->dangerLevel->name == 'Tehlikeli' ? 'warning' : 'danger') }} fs-6">
                                        {{ $record->dangerLevel->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">İş Yeri Hekimi</label>
                            <p class="fw-bold mb-0">
                                @if($record->doctor_name)
                                    <i class="bi bi-person-badge text-success"></i> {{ $record->doctor_name }}
                                @else
                                    <span class="text-muted">Atanmadı</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Sağlık Personeli</label>
                            <p class="fw-bold mb-0">
                                @if($record->health_staff_name)
                                    <i class="bi bi-person-badge text-info"></i> {{ $record->health_staff_name }}
                                @else
                                    <span class="text-muted">Atanmadı</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">İş Güvenliği Uzmanı</label>
                            <p class="fw-bold mb-0">
                                @if($record->safety_expert_name)
                                    <i class="bi bi-shield-check text-warning"></i> {{ $record->safety_expert_name }}
                                @else
                                    <span class="text-muted">Atanmadı</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Mali Müşavir</label>
                            <p class="fw-bold mb-0">
                                @if($record->accountant_name)
                                    <i class="bi bi-calculator text-primary"></i> {{ $record->accountant_name }}
                                @else
                                    <span class="text-muted">Atanmadı</span>
                                @endif
                            </p>
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Sözleşmeyi Yapan</label>
                            <p class="fw-bold mb-0">
                                @if($record->contract_creator_name)
                                    <i class="bi bi-person-check text-success"></i> {{ $record->contract_creator_name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Sözleşme Süresi</label>
                            <p class="fw-bold mb-0">{{ $record->contract_months ? $record->contract_months . ' Ay' : '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Sözleşme Başlangıç</label>
                            <p class="fw-bold mb-0">
                                @if($record->contract_start)
                                    <i class="bi bi-calendar-check"></i> {{ \Carbon\Carbon::parse($record->contract_start)->format('d.m.Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label text-muted small">Sözleşme Bitiş</label>
                            <p class="fw-bold mb-0">
                                @if($record->contract_end)
                                    <i class="bi bi-calendar-x"></i> {{ \Carbon\Carbon::parse($record->contract_end)->format('d.m.Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
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
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">Aylık Fiyat</label>
                            <p class="fw-bold mb-0">{{ $record->monthly_price ? number_format($record->monthly_price, 2, ',', '.') . ' ₺' : '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">KDV</label>
                            <p class="fw-bold mb-0">{{ $record->monthly_kdv ? number_format($record->monthly_kdv, 2, ',', '.') . ' ₺' : '-' }}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label text-muted small">Toplam</label>
                            <p class="fw-bold mb-0 text-success fs-5">{{ $record->monthly_total ? number_format($record->monthly_total, 2, ',', '.') . ' ₺' : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 9. RANDEVU BİLGİLERİ --}}
            @if($record->appointment_date || $record->appointment_time)
                <div class="card mb-3 mb-md-4">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-calendar-check"></i> Randevu Bilgileri
                    </div>
                    <div class="card-body">
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label text-muted small">Randevu Tarihi</label>
                                <p class="fw-bold mb-0">
                                    @if($record->appointment_date)
                                        <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($record->appointment_date)->format('d.m.Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label text-muted small">Randevu Saati</label>
                                <p class="fw-bold mb-0">
                                    @if($record->appointment_time)
                                        <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($record->appointment_time)->format('H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 10. NOTLAR --}}
            @if($record->notes)
                <div class="card mb-3 mb-md-4">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-sticky"></i> Notlar
                    </div>
                    <div class="card-body">
                        <p class="mb-0 small">{{ $record->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection