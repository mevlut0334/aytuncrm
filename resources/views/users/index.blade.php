@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Kullanıcı Listesi</h4>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Yeni Kullanıcı
                    </a>
                </div>

                <div class="card-body">
                    {{-- Filtreleme Formu --}}
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" 
                                       name="name" 
                                       class="form-control" 
                                       placeholder="İsim ara..." 
                                       value="{{ request('name') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="email" 
                                       name="email" 
                                       class="form-control" 
                                       placeholder="E-posta ara..." 
                                       value="{{ request('email') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select">
                                    <option value="">Tüm Roller</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-search"></i> Filtrele
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Başarı Mesajı --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Kullanıcı Tablosu --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>İsim</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th>Durum</th>
                                    <th>Kayıt Tarihi</th>
                                    @if(auth()->user()->role === 'admin')
                                        <th class="text-end">İşlemler</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                {{-- ✅ FOREACH DÖNGÜSÜ - Bu kısım eksikti --}}
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->is_active ?? true)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                        
                                        {{-- Admin İşlemleri --}}
                                        @if(auth()->user()->role === 'admin')
                                            <td class="text-end">
                                                {{-- Düzenle Butonu (İsteğe bağlı) --}}
                                                {{--
                                                <a href="{{ route('users.edit', $user->id) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                --}}
                                                
                                                {{-- Silme Butonu --}}
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Sil">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            disabled 
                                                            title="Kendinizi silemezsiniz">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Kullanıcı bulunamadı.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection