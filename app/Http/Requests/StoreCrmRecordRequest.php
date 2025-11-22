<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCrmRecordRequest extends FormRequest
{
    /**
     * Kullanıcı bu isteği yapabilir mi?
     */
    public function authorize(): bool
    {
        return true; // Auth middleware zaten kontrol ediyor
    }

    /**
     * Validation kuralları
     */
    public function rules(): array
    {
        return [
            // Firma Bilgileri
            'file_number' => ['required', 'string', 'max:255'], // ✅ unique kaldırıldı
            'company_title' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string'],
            
            // Lokasyon Bilgileri
            'province_id' => ['required', 'exists:provinces,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            
            // Vergi ve Resmi Bilgiler
            'tax_office' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:255'], // ✅ unique kaldırıldı
            'sgk_number' => ['nullable', 'string', 'max:255'],
            'trade_register_no' => ['nullable', 'string', 'max:255'],
            'identity_no' => ['nullable', 'string', 'max:11'], // TC Kimlik 11 haneli
            
            // Yetkili Bilgileri
            'officer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            
            // İş Yeri Bilgileri
            'personnel_count' => ['required', 'integer', 'min:0'],
            'danger_level_id' => ['required', 'exists:danger_levels,id'],
            
            // Personel Atamaları (Hepsi opsiyonel)
            'doctor_id' => ['nullable', 'exists:personnels,id'],
            'health_staff_id' => ['nullable', 'exists:personnels,id'],
            'safety_expert_id' => ['nullable', 'exists:personnels,id'],
            'accountant_id' => ['nullable', 'exists:personnels,id'],

            // Personel Adları (Text - opsiyonel)
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'health_staff_name' => ['nullable', 'string', 'max:255'],
            'safety_expert_name' => ['nullable', 'string', 'max:255'],
            'accountant_name' => ['nullable', 'string', 'max:255'],
            
            // Sözleşme Bilgileri
            'contract_creator_id' => ['nullable', 'exists:users,id'],
            'contract_creator_name' => ['required', 'string', 'max:255'],
            'contract_start' => ['required', 'date'],
            'contract_end' => ['required', 'date', 'after:contract_start'],
            'contract_months' => ['required', 'integer', 'min:1', 'max:120'],
            
            // Fiyat Bilgileri
            'monthly_price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'monthly_kdv' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'monthly_total' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            
            // Randevu Bilgileri (Opsiyonel)
            'appointment_date' => ['nullable', 'date'],
            'appointment_time' => ['nullable', 'date_format:H:i'],
            
            // Notlar
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Türkçe hata mesajları
     */
    public function messages(): array
    {
        return [
            'file_number.required' => 'Dosya numarası zorunludur.',
            'company_title.required' => 'Firma unvanı zorunludur.',
            'company_address.required' => 'Firma adresi zorunludur.',
            
            'province_id.required' => 'İl seçimi zorunludur.',
            'province_id.exists' => 'Seçilen il geçersiz.',
            'district_id.required' => 'İlçe seçimi zorunludur.',
            'district_id.exists' => 'Seçilen ilçe geçersiz.',
            
            'tax_office.required' => 'Vergi dairesi zorunludur.',
            'tax_number.required' => 'Vergi numarası zorunludur.',
            'identity_no.max' => 'TC Kimlik numarası en fazla 11 haneli olabilir.',
            
            'officer_name.required' => 'Yetkili adı zorunludur.',
            'phone.required' => 'Telefon numarası zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            
            'personnel_count.required' => 'Personel sayısı zorunludur.',
            'personnel_count.integer' => 'Personel sayısı rakam olmalıdır.',
            'personnel_count.min' => 'Personel sayısı 0\'dan küçük olamaz.',
            
            'danger_level_id.required' => 'Tehlike sınıfı seçimi zorunludur.',
            'danger_level_id.exists' => 'Seçilen tehlike sınıfı geçersiz.',
            
            'contract_creator_name.required' => 'Sözleşmeyi yapan kişi adı zorunludur.',
            'contract_start.required' => 'Sözleşme başlangıç tarihi zorunludur.',
            'contract_start.date' => 'Geçerli bir tarih giriniz.',
            'contract_end.required' => 'Sözleşme bitiş tarihi zorunludur.',
            'contract_end.after' => 'Bitiş tarihi, başlangıç tarihinden sonra olmalıdır.',
            'contract_months.required' => 'Sözleşme süresi zorunludur.',
            
            'monthly_price.required' => 'Aylık fiyat zorunludur.',
            'monthly_price.numeric' => 'Aylık fiyat rakam olmalıdır.',
            'monthly_kdv.required' => 'KDV tutarı zorunludur.',
            'monthly_total.required' => 'Toplam tutar zorunludur.',
            
            'appointment_time.date_format' => 'Randevu saati HH:MM formatında olmalıdır.',
        ];
    }

    /**
     * Türkçe alan isimleri
     */
    public function attributes(): array
    {
        return [
            'file_number' => 'dosya numarası',
            'company_title' => 'firma unvanı',
            'company_address' => 'firma adresi',
            'province_id' => 'il',
            'district_id' => 'ilçe',
            'neighborhood' => 'mahalle',
            'tax_office' => 'vergi dairesi',
            'tax_number' => 'vergi numarası',
            'sgk_number' => 'SGK numarası',
            'trade_register_no' => 'ticaret sicil numarası',
            'identity_no' => 'TC kimlik numarası',
            'officer_name' => 'yetkili adı',
            'phone' => 'telefon',
            'email' => 'e-posta',
            'personnel_count' => 'personel sayısı',
            'danger_level_id' => 'tehlike sınıfı',
            'doctor_id' => 'doktor',
            'health_staff_id' => 'sağlık personeli',
            'safety_expert_id' => 'iş güvenliği uzmanı',
            'accountant_id' => 'muhasebeci',
            'doctor_name' => 'iş yeri hekimi',
            'health_staff_name' => 'sağlık personeli',
            'safety_expert_name' => 'iş güvenliği uzmanı',
            'accountant_name' => 'mali müşavir',
            'contract_creator_id' => 'sözleşme oluşturan',
            'contract_creator_name' => 'sözleşmeyi yapan',
            'contract_start' => 'sözleşme başlangıcı',
            'contract_end' => 'sözleşme bitişi',
            'contract_months' => 'sözleşme süresi',
            'monthly_price' => 'aylık fiyat',
            'monthly_kdv' => 'KDV',
            'monthly_total' => 'toplam tutar',
            'appointment_date' => 'randevu tarihi',
            'appointment_time' => 'randevu saati',
            'notes' => 'notlar',
        ];
    }
}