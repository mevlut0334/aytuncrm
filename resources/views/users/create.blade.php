@extends('layouts.app')

@section('title', 'Yeni Kullanıcı Ekle - İSG CRM')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-plus"></i> Yeni Kullanıcı Ekle</h2>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Geri Dön
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Ad Soyad -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                Ad Soyad <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Kullanıcı adı soyadı"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- E-posta -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                E-posta <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="ornek@email.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Şifre -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Şifre <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="En az 8 karakter"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Şifre en az 8 karakter olmalıdır.
                            </div>
                        </div>

                        <!-- Rol -->
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">
                                Kullanıcı Rolü <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Rol Seçiniz</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                    Admin - Tüm yetkiler
                                </option>
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>
                                    Kullanıcı - Sadece veri girişi
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> 
                                <strong>Admin:</strong> Tüm işlemleri yapabilir. 
                                <strong>Kullanıcı:</strong> Sadece veri girişi ve görüntüleme yapabilir.
                            </div>
                        </div>

                        <!-- Durum -->
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="bi bi-check-circle"></i> Aktif kullanıcı
                                </label>
                                <div class="form-text">
                                    Pasif kullanıcılar sisteme giriş yapamaz.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Butonlar -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Kullanıcıyı Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bilgilendirme Kutusu -->
        <div class="alert alert-info mt-3" role="alert">
            <i class="bi bi-info-circle-fill"></i> 
            <strong>Önemli:</strong> 
            Kullanıcı hesabı oluşturulduktan sonra, giriş bilgileri kullanıcıya manuel olarak iletilmelidir. 
            Sistem otomatik kayıt işlemi yapmamaktadır.
        </div>
    </div>
</div>
@endsection