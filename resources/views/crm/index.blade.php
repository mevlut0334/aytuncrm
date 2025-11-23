@extends('layouts.app')

@section('title', 'Firma Listesi - İSG CRM')

@push('styles')
<style>
    /* Mobil için kart görünümü */
    @media (max-width: 767.98px) {
        .mobile-card {
            display: block;
        }
        .desktop-table {
            display: none;
        }
        .company-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .company-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
    }

    /* Desktop için tablo görünümü */
    @media (min-width: 768px) {
        .mobile-card {
            display: none;
        }
        .desktop-table {
            display: block;
        }
    }

    /* Filtre kartı responsive */
    @media (max-width: 575.98px) {
        .filter-input {
            font-size: 0.9rem;
        }
        .form-label.small {
            font-size: 0.8rem;
        }
    }

    /* Buton grubu responsive */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    @media (max-width: 575.98px) {
        .action-buttons {
            flex-direction: column;
        }
        .action-buttons .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Başlık ve Butonlar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-2">
        <h2 class="mb-0"><i class="bi bi-building"></i> Firma Listesi</h2>
        
        <div class="action-buttons w-100 w-md-auto">
            {{-- Excel Export Butonları - Sadece Admin --}}
            @if(auth()->user()->role === 'admin')
                {{-- Tümünü Dışa Aktar --}}
                <a href="{{ route('crm.export') }}" 
                   class="btn btn-success btn-sm"
                   title="Tüm firmaları Excel olarak indir">
                    <i class="bi bi-file-earmark-excel"></i> 
                    <span class="d-none d-sm-inline">Tümünü</span> Dışa Aktar
                </a>
                
                {{-- Filtrelenmiş Verileri Dışa Aktar --}}
                @if(request()->hasAny(['file_number', 'company_title', 'danger_level', 'doctor_name', 'health_staff_name', 'safety_expert_name', 'accountant_name', 'contract_creator']))
                    <a href="{{ route('crm.export', request()->query()) }}" 
                       class="btn btn-outline-success btn-sm"
                       title="Filtrelenmiş verileri Excel olarak indir">
                        <i class="bi bi-funnel"></i> 
                        <span class="d-none d-sm-inline">Filtreyi</span> Dışa Aktar
                    </a>
                @endif
            @endif
            
            {{-- Yeni Firma Ekle Butonu --}}
            <a href="{{ route('crm.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Yeni Firma
            </a>
        </div>
    </div>

    {{-- Filtreleme Kartı --}}
    <div class="card mb-3 mb-md-4">
        <div class="card-header bg-light">
            <i class="bi bi-funnel"></i> Filtreleme
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('crm.index') }}" id="filterForm">
                <div class="row g-2 g-md-3">
                    {{-- Filtre alanları --}}
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">Dosya Numarası</label>
                        <input type="text" 
                               name="file_number" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Dosya no..."
                               value="{{ request('file_number') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">Firma Unvanı</label>
                        <input type="text" 
                               name="company_title" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Firma adı..."
                               value="{{ request('company_title') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">Tehlike Sınıfı</label>
                        <input type="text" 
                               name="danger_level" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Tehlike sınıfı..."
                               value="{{ request('danger_level') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">İş Yeri Hekimi</label>
                        <input type="text" 
                               name="doctor_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Hekim..."
                               value="{{ request('doctor_name') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">Sağlık Personeli</label>
                        <input type="text" 
                               name="health_staff_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Sağlık personeli..."
                               value="{{ request('health_staff_name') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">İş Güvenliği Uzmanı</label>
                        <input type="text" 
                               name="safety_expert_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Uzman..."
                               value="{{ request('safety_expert_name') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">Mali Müşavir</label>
                        <input type="text" 
                               name="accountant_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Mali müşavir..."
                               value="{{ request('accountant_name') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small">Sözleşmeyi Yapan</label>
                        <input type="text" 
                               name="contract_creator" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Sözleşmeyi yapan..."
                               value="{{ request('contract_creator') }}">
                    </div>
                </div>

                {{-- Filtre Butonları --}}
                <div class="row mt-2 mt-md-3">
                    <div class="col-12">
                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                            <a href="{{ route('crm.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-circle"></i> Temizle
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i> Filtrele
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MOBİL: Kart Görünümü --}}
    <div class="mobile-card">
        @forelse($records as $record)
            <div class="card company-card" onclick="window.location='{{ route('crm.show', $record->id) }}'">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $record->company_title }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-folder"></i> {{ $record->file_number }}
                            </small>
                        </div>
                        @if($record->dangerLevel)
                            <span class="badge bg-{{ $record->dangerLevel->name == 'Az Tehlikeli' ? 'success' : ($record->dangerLevel->name == 'Tehlikeli' ? 'warning' : 'danger') }}">
                                {{ $record->dangerLevel->name }}
                            </span>
                        @endif
                    </div>

                    <hr class="my-2">

                    <div class="small">
                        @if($record->doctor_name)
                            <div class="mb-1">
                                <i class="bi bi-person-badge text-success"></i>
                                <strong>Hekim:</strong> {{ $record->doctor_name }}
                            </div>
                        @endif

                        @if($record->health_staff_name)
                            <div class="mb-1">
                                <i class="bi bi-heart-pulse text-info"></i>
                                <strong>Sağlık:</strong> {{ $record->health_staff_name }}
                            </div>
                        @endif

                        @if($record->safety_expert_name)
                            <div class="mb-1">
                                <i class="bi bi-shield-check text-warning"></i>
                                <strong>İSG:</strong> {{ $record->safety_expert_name }}
                            </div>
                        @endif

                        @if($record->accountant_name)
                            <div class="mb-1">
                                <i class="bi bi-calculator text-primary"></i>
                                <strong>Mali:</strong> {{ $record->accountant_name }}
                            </div>
                        @endif

                        @if($record->contract_creator_name)
                            <div class="mb-1">
                                <i class="bi bi-person-check text-secondary"></i>
                                <strong>Sözleşme:</strong> {{ $record->contract_creator_name }}
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-1 mt-2" onclick="event.stopPropagation();">
                        <a href="{{ route('crm.show', $record->id) }}" 
                           class="btn btn-sm btn-info" 
                           title="Detay">
                            <i class="bi bi-eye"></i>
                        </a>
                        
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('crm.edit', $record->id) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Düzenle">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('crm.destroy', $record->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Bu firmayı silmek istediğinizden emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center text-muted py-4">
                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                    Kayıtlı firma bulunamadı.
                </div>
            </div>
        @endforelse
    </div>

    {{-- DESKTOP: Tablo Görünümü --}}
    <div class="desktop-table">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Dosya No</th>
                                <th>Firma Unvanı</th>
                                <th>Tehlike Sınıfı</th>
                                <th>İş Yeri Hekimi</th>
                                <th>Sağlık Personeli</th>
                                <th>İş Güv. Uzmanı</th>
                                <th>Mali Müşavir</th>
                                <th>Sözleşmeyi Yapan</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('crm.show', $record->id) }}'">
                                    <td><strong>{{ $record->file_number }}</strong></td>
                                    <td><strong>{{ $record->company_title }}</strong></td>
                                    <td>
                                        @if($record->dangerLevel)
                                            <span class="badge bg-{{ $record->dangerLevel->name == 'Az Tehlikeli' ? 'success' : ($record->dangerLevel->name == 'Tehlikeli' ? 'warning' : 'danger') }}">
                                                {{ $record->dangerLevel->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->doctor_name)
                                            <i class="bi bi-person-badge text-success"></i> {{ $record->doctor_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->health_staff_name)
                                            <i class="bi bi-heart-pulse text-info"></i> {{ $record->health_staff_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->safety_expert_name)
                                            <i class="bi bi-shield-check text-warning"></i> {{ $record->safety_expert_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->accountant_name)
                                            <i class="bi bi-calculator text-primary"></i> {{ $record->accountant_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->contract_creator_name ?? '-' }}</td>
                                    <td class="text-center" onclick="event.stopPropagation();">
                                        <a href="{{ route('crm.show', $record->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Detay">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if(auth()->user()->role === 'admin')
                                            <a href="{{ route('crm.edit', $record->id) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('crm.destroy', $record->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bu firmayı silmek istediğinizden emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        Kayıtlı firma bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($records->hasPages())
        <div class="d-flex justify-content-center mt-3 mt-md-4">
            {{ $records->withQueryString()->links() }}
        </div>
    @endif

    {{-- Toplam Kayıt Bilgisi --}}
    <div class="text-muted small mt-2 text-center text-md-start">
        Toplam {{ $records->total() }} firma kayıtlı.
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let typingTimer;
    const doneTypingInterval = 500;

    document.querySelectorAll('.filter-input').forEach(function(input) {
        input.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            const value = this.value.trim();
            
            if (value.length >= 3 || value.length === 0) {
                typingTimer = setTimeout(function() {
                    document.getElementById('filterForm').submit();
                }, doneTypingInterval);
            }
        });
    });
});
</script>
@endpush