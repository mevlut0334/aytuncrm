<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CrmRecordController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

// ============================================
// ANA SAYFA - Laravel Welcome (Giriş yapılmamışsa)
// ============================================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('crm.index');
    }
    return view('welcome');
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
// DASHBOARD - CRM Index'e yönlendir (geriye uyumluluk için)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('crm.index');
    })->name('dashboard');
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
// 👥 TÜM KULLANICILAR - CRM Modülü
// ============================================
Route::middleware(['auth'])->group(function () {

    // ============================================
    // 🏢 CRM - Firma Kayıtları Modülü
    // ============================================
    Route::prefix('crm')->name('crm.')->group(function () {
        // Görüntüleme ve Ekleme - Herkes yapabilir
        Route::get('/', [CrmRecordController::class, 'index'])->name('index');
        Route::get('/create', [CrmRecordController::class, 'create'])->name('create');
        Route::post('/', [CrmRecordController::class, 'store'])->name('store');

        // ✅ Excel Export - Sadece Admin (/{id}'den ÖNCE olmalı!)
        Route::get('/export', [CrmRecordController::class, 'export'])->name('export');

        Route::get('/{id}', [CrmRecordController::class, 'show'])->name('show');

        // Düzenleme ve Silme - Sadece Admin
        Route::middleware([\App\Http\Middleware\PreventDataModification::class])->group(function () {
            Route::get('/{id}/edit', [CrmRecordController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CrmRecordController::class, 'update'])->name('update');
            Route::delete('/{id}', [CrmRecordController::class, 'destroy'])->name('destroy');
        });
    });

    // ============================================
    // 🔔 HATIRLATMALAR - Tüm Kullanıcılar
    // ============================================
    Route::prefix('reminders')->name('reminders.')->group(function () {
        Route::get('/', [ReminderController::class, 'index'])->name('index');
        Route::get('/create', [ReminderController::class, 'create'])->name('create');
        Route::post('/', [ReminderController::class, 'store'])->name('store');
        Route::get('/{reminder}', [ReminderController::class, 'show'])->name('show');
        Route::get('/{reminder}/edit', [ReminderController::class, 'edit'])->name('edit');
        Route::put('/{reminder}', [ReminderController::class, 'update'])->name('update');
        Route::delete('/{reminder}', [ReminderController::class, 'destroy'])->name('destroy');
        Route::post('/{reminder}/toggle-complete', [ReminderController::class, 'toggleComplete'])
            ->name('toggle-complete');
    });

    // ============================================
    // 📍 AJAX - İlçeleri Getir (İl seçilince)
    // ============================================
    Route::get('/api/districts/{province_id}', [CrmRecordController::class, 'getDistricts'])
        ->name('api.districts');

});
