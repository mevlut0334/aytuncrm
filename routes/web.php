<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

// ============================================
// ANA SAYFA - Laravel Welcome (Giriş yapılmamışsa)
// ============================================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome'); // Laravel varsayılan sayfası
})->name('home');

// ============================================
// LOGIN & LOGOUT
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ============================================
// DASHBOARD - Tüm kullanıcılar erişebilir
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ============================================
// 🔒 SADECE ADMIN - Users Modülü (Tamamen Kapalı)
// ============================================
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// ============================================
// 👥 TÜM KULLANICILAR - Diğer Modüller
// Normal kullanıcı: Görüntüleme + Ekleme ✅ | Düzenleme + Silme ❌
// Admin: Her şey ✅
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Örnek: Müşteriler Modülü (ileride eklenecek)
    /*
    Route::prefix('customers')->name('customers.')->group(function () {
        // Görüntüleme ve Ekleme - Herkes yapabilir
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        
        // Düzenleme ve Silme - Sadece Admin
        Route::middleware([\App\Http\Middleware\PreventDataModification::class])->group(function () {
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });
    });
    */
    
    // Örnek: Firmalar Modülü (ileride eklenecek)
    /*
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        
        Route::middleware([\App\Http\Middleware\PreventDataModification::class])->group(function () {
            Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
            Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
            Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
        });
    });
    */
});