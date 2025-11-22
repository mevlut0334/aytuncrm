@extends('layouts.app')

@section('title', 'Firma Listesi - İSG CRM')

@section('content')
<div class="container-fluid">
    {{-- Başlık ve Yeni Firma Butonu --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building"></i> Firma Listesi</h2>
    
    <div class="d-flex gap-2">
        {{-- Excel Export Butonları - Sadece Admin --}}
        @if(auth()->user()->role === 'admin')
            {{-- Tümünü Dışa Aktar --}}
            <a href="{{ route('crm.export') }}" 
               class="btn btn-success"
               title="Tüm firmaları Excel olarak indir">
                <i class="bi bi-file-earmark-excel"></i> Tümünü Dışa Aktar
            </a>
            
            {{-- Filtrelenmiş Verileri Dışa Aktar - Sadece filtre varsa görünür --}}
            @if(request()->hasAny(['file_number', 'company_title', 'danger_level', 'doctor_name', 'health_staff_name', 'safety_expert_name', 'accountant_name', 'contract_creator']))
                <a href="{{ route('crm.export', request()->query()) }}" 
                   class="btn btn-outline-success"
                   title="Filtrelenmiş verileri Excel olarak indir">
                    <i class="bi bi-funnel"></i> Filtreyi Dışa Aktar
                </a>
            @endif
        @endif
        
        {{-- Yeni Firma Ekle Butonu --}}
        <a href="{{ route('crm.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Firma Ekle
        </a>
    </div>
</div>

    {{-- Filtreleme Kartı --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <i class="bi bi-funnel"></i> Filtreleme
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('crm.index') }}" id="filterForm">
                <div class="row g-3">
                    {{-- 1. Dosya Numarası --}}
                    <div class="col-md-3">
                        <label class="form-label small">Dosya Numarası</label>
                        <input type="text" 
                               name="file_number" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Dosya no ara..."
                               value="{{ request('file_number') }}">
                    </div>

                    {{-- 2. Firma Unvanı --}}
                    <div class="col-md-3">
                        <label class="form-label small">Firma Unvanı</label>
                        <input type="text" 
                               name="company_title" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Firma adı ara..."
                               value="{{ request('company_title') }}">
                    </div>

                    {{-- 3. Tehlike Sınıfı --}}
                    <div class="col-md-3">
                        <label class="form-label small">Tehlike Sınıfı</label>
                        <input type="text" 
                               name="danger_level" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Tehlike sınıfı ara..."
                               value="{{ request('danger_level') }}">
                    </div>

                    {{-- 4. İş Yeri Hekimi --}}
                    <div class="col-md-3">
                        <label class="form-label small">İş Yeri Hekimi</label>
                        <input type="text" 
                               name="doctor_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Hekim ara..."
                               value="{{ request('doctor_name') }}">
                    </div>

                    {{-- 5. Sağlık Personeli --}}
                    <div class="col-md-3">
                        <label class="form-label small">Sağlık Personeli</label>
                        <input type="text" 
                               name="health_staff_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Sağlık personeli ara..."
                               value="{{ request('health_staff_name') }}">
                    </div>

                    {{-- 6. İş Güvenliği Uzmanı --}}
                    <div class="col-md-3">
                        <label class="form-label small">İş Güvenliği Uzmanı</label>
                        <input type="text" 
                               name="safety_expert_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Uzman ara..."
                               value="{{ request('safety_expert_name') }}">
                    </div>

                    {{-- 7. Mali Müşavir --}}
                    <div class="col-md-3">
                        <label class="form-label small">Mali Müşavir</label>
                        <input type="text" 
                               name="accountant_name" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Mali müşavir ara..."
                               value="{{ request('accountant_name') }}">
                    </div>

                    {{-- 8. Sözleşmeyi Yapan --}}
                    <div class="col-md-3">
                        <label class="form-label small">Sözleşmeyi Yapan</label>
                        <input type="text" 
                               name="contract_creator" 
                               class="form-control form-control-sm filter-input" 
                               placeholder="Sözleşmeyi yapan ara..."
                               value="{{ request('contract_creator') }}">
                    </div>
                </div>

                {{-- Filtre Butonları --}}
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <a href="{{ route('crm.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Temizle
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i> Filtrele
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Firma Tablosu --}}
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
                                {{-- 1. Dosya No --}}
                                <td><strong>{{ $record->file_number }}</strong></td>
                                
                                {{-- 2. Firma Unvanı --}}
                                <td>
                                    <strong>{{ $record->company_title }}</strong>
                                </td>
                                
                                {{-- 3. Tehlike Sınıfı --}}
                                <td>
                                    @if($record->dangerLevel)
                                        <span class="badge bg-{{ $record->dangerLevel->name == 'Az Tehlikeli' ? 'success' : ($record->dangerLevel->name == 'Tehlikeli' ? 'warning' : 'danger') }}">
                                            {{ $record->dangerLevel->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- 4. İş Yeri Hekimi --}}
                                <td>
                                    @if($record->doctor_name)
                                        <i class="bi bi-person-badge text-success"></i> {{ $record->doctor_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- 5. Sağlık Personeli --}}
                                <td>
                                    @if($record->health_staff_name)
                                        <i class="bi bi-heart-pulse text-info"></i> {{ $record->health_staff_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- 6. İş Güvenliği Uzmanı --}}
                                <td>
                                    @if($record->safety_expert_name)
                                        <i class="bi bi-shield-check text-warning"></i> {{ $record->safety_expert_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- 7. Mali Müşavir --}}
                                <td>
                                    @if($record->accountant_name)
                                        <i class="bi bi-calculator text-primary"></i> {{ $record->accountant_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- 8. Sözleşmeyi Yapan --}}
                                <td>{{ $record->contract_creator_name ?? '-' }}</td>
                                
                                {{-- İşlemler --}}
                                <td class="text-center" onclick="event.stopPropagation();">
                                    {{-- Detay Butonu - Herkes --}}
                                    <a href="{{ route('crm.show', $record->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Detay">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    {{-- Düzenle ve Sil - Sadece Admin --}}
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

            {{-- Pagination --}}
            @if($records->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $records->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Toplam Kayıt Bilgisi --}}
    <div class="text-muted small mt-2">
        Toplam {{ $records->total() }} firma kayıtlı.
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let typingTimer;
    const doneTypingInterval = 500; // 500ms bekle

    // Text inputlar için (3 harf yazınca otomatik arama)
    document.querySelectorAll('.filter-input').forEach(function(input) {
        input.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            const value = this.value.trim();
            
            // 3 veya daha fazla karakter girildiğinde veya alan boşaltıldığında
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