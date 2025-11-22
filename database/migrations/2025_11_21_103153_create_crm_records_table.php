<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_records', function (Blueprint $table) {
            $table->id();
            
            // Firma Bilgileri
            $table->string('file_number')->index(); // Dosya numarası - index
            $table->string('company_title')->index(); // Firma unvanı - arama için index
            $table->text('company_address');
            
            // Lokasyon Bilgileri
            $table->foreignId('province_id')->constrained('provinces')->onDelete('restrict');
            $table->foreignId('district_id')->constrained('districts')->onDelete('restrict');
            $table->string('neighborhood')->nullable();
            
            // Vergi ve Resmi Bilgiler
            $table->string('tax_office');
            $table->string('tax_number')->index(); // Vergi no - index
            $table->string('sgk_number')->nullable();
            $table->string('trade_register_no')->nullable();
            $table->string('identity_no')->nullable(); // TC Kimlik No
            
            // Yetkili Bilgileri
            $table->string('officer_name');
            $table->string('phone')->index(); // Telefon - arama için index
            $table->string('email')->nullable()->index(); // Email - arama için index
            
            // İş Yeri Bilgileri
            $table->integer('personnel_count')->default(0);
            $table->foreignId('danger_level_id')->constrained('danger_levels')->onDelete('restrict');
            
            // Personel Atamaları
            $table->foreignId('doctor_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->foreignId('health_staff_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->foreignId('safety_expert_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->foreignId('accountant_id')->nullable()->constrained('personnels')->onDelete('set null');
            
            // Sözleşme Bilgileri
            $table->foreignId('contract_creator_id')->constrained('users')->onDelete('restrict');
            $table->date('contract_start')->index(); // Sözleşme aramaları için index
            $table->date('contract_end')->index(); // Bitiş tarihi aramaları için index
            $table->integer('contract_months')->default(12);
            
            // Fiyat Bilgileri
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('monthly_kdv', 10, 2)->default(0);
            $table->decimal('monthly_total', 10, 2)->default(0);
            
            // Randevu Bilgileri
            $table->date('appointment_date')->nullable()->index(); // Randevu aramaları için index
            $table->time('appointment_time')->nullable();
            
            // Notlar
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Composite Indexes - Performans için
            $table->index(['province_id', 'district_id']); // Lokasyon bazlı aramalar
            $table->index(['contract_start', 'contract_end']); // Sözleşme periyot aramaları
            $table->index(['danger_level_id', 'personnel_count']); // Tehlike seviyesi bazlı istatistikler
            $table->index(['doctor_id', 'safety_expert_id']); // Personel atama sorguları
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_records');
    }
};