<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCrmRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Firma Bilgileri - ZORUNLU
            'file_number' => ['required', 'string', 'max:255'],
            'company_title' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string'],
            'service_address' => ['nullable', 'string'],
            
            // Lokasyon - ZORUNLU
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            
            // Vergi ve Resmi Bilgiler - ZORUNLU
            'tax_office' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:255'],
            'sgk_number' => ['nullable', 'string', 'max:255'],
            'trade_register_no' => ['nullable', 'string', 'max:255'],
            'identity_no' => ['nullable', 'string', 'max:11'],
            
            // Yetkili Bilgileri - OPSİYONEL
            'officer_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            
            // İş Yeri Bilgileri - OPSİYONEL
            'personnel_count' => ['nullable', 'integer', 'min:0'],
            'danger_level_id' => ['nullable', 'integer', 'exists:danger_levels,id'],
            
            // Personel Atamaları (opsiyonel)
            'doctor_id' => ['nullable', 'integer', 'exists:personnels,id'],
            'health_staff_id' => ['nullable', 'integer', 'exists:personnels,id'],
            'safety_expert_id' => ['nullable', 'integer', 'exists:personnels,id'],
            'accountant_id' => ['nullable', 'integer', 'exists:personnels,id'],
            
            // Personel Adları (Text - opsiyonel)
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'health_staff_name' => ['nullable', 'string', 'max:255'],
            'safety_expert_name' => ['nullable', 'string', 'max:255'],
            'accountant_name' => ['nullable', 'string', 'max:255'],
            
            // Sözleşme Bilgileri - OPSİYONEL
            'contract_creator_id' => ['nullable', 'integer', 'exists:users,id'],
            'contract_creator_name' => ['nullable', 'string', 'max:255'],
            'contract_start' => ['nullable', 'date'],
            'contract_end' => ['nullable', 'date', 'after_or_equal:contract_start'],
            'contract_months' => ['nullable', 'integer', 'min:1', 'max:120'],
            
            // Fiyat Bilgileri - OPSİYONEL
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'monthly_kdv' => ['nullable', 'numeric', 'min:0'],
            'monthly_total' => ['nullable', 'numeric', 'min:0'],
            
            // Randevu Bilgileri
            'appointment_date' => ['nullable', 'date'],
            'appointment_time' => ['nullable', 'date_format:H:i'],
            
            // Notlar
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'file_number' => 'Dosya Numarası',
            'company_title' => 'Firma Unvanı',
            'company_address' => 'Firma Adresi',
            'service_address' => 'Hizmet Adresi',
            'province_id' => 'İl',
            'district_id' => 'İlçe',
            'neighborhood' => 'Mahalle',
            'tax_office' => 'Vergi Dairesi',
            'tax_number' => 'Vergi Numarası',
            'sgk_number' => 'SGK Numarası',
            'trade_register_no' => 'Ticaret Sicil No',
            'identity_no' => 'TC Kimlik No',
            'officer_name' => 'Yetkili Adı Soyadı',
            'phone' => 'Telefon',
            'email' => 'E-posta',
            'personnel_count' => 'Personel Sayısı',
            'danger_level_id' => 'Tehlike Sınıfı',
            'doctor_id' => 'İş Yeri Hekimi',
            'health_staff_id' => 'Sağlık Personeli',
            'safety_expert_id' => 'İş Güvenliği Uzmanı',
            'accountant_id' => 'Mali Müşavir',
            'doctor_name' => 'İş Yeri Hekimi',
            'health_staff_name' => 'Sağlık Personeli',
            'safety_expert_name' => 'İş Güvenliği Uzmanı',
            'accountant_name' => 'Mali Müşavir',
            'contract_creator_id' => 'Sözleşmeyi Yapan',
            'contract_creator_name' => 'Sözleşmeyi Yapan',
            'contract_start' => 'Sözleşme Başlangıç',
            'contract_end' => 'Sözleşme Bitiş',
            'contract_months' => 'Sözleşme Süresi (Ay)',
            'monthly_price' => 'Aylık Ücret',
            'monthly_kdv' => 'KDV',
            'monthly_total' => 'Toplam',
            'appointment_date' => 'Randevu Tarihi',
            'appointment_time' => 'Randevu Saati',
            'notes' => 'Notlar',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'contract_end.after_or_equal' => 'Sözleşme bitiş tarihi, başlangıç tarihinden önce olamaz.',
            'identity_no.max' => 'TC Kimlik No 11 haneden fazla olamaz.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'province_id.exists' => 'Seçilen il geçersiz.',
            'district_id.exists' => 'Seçilen ilçe geçersiz.',
        ];
    }
}