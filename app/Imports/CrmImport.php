<?php

namespace App\Imports;

use App\Models\CrmRecord;
use App\Models\Province;
use App\Models\District;
use App\Models\DangerLevel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class CrmImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    // Default ilçe mapping'i (İl merkezi için)
    private $defaultDistricts = [
        'Adana' => 'Seyhan',
        'Adıyaman' => 'Merkez',
        'Afyonkarahisar' => 'Merkez',
        'Ağrı' => 'Merkez',
        'Aksaray' => 'Merkez',
        'Amasya' => 'Merkez',
        'Ankara' => 'Çankaya',
        'Antalya' => 'Muratpaşa',
        'Ardahan' => 'Merkez',
        'Artvin' => 'Merkez',
        'Aydın' => 'Efeler',
        'Balıkesir' => 'Karesi',
        'Bartın' => 'Merkez',
        'Batman' => 'Merkez',
        'Bayburt' => 'Merkez',
        'Bilecik' => 'Merkez',
        'Bingöl' => 'Merkez',
        'Bitlis' => 'Merkez',
        'Bolu' => 'Merkez',
        'Burdur' => 'Merkez',
        'Bursa' => 'Osmangazi',
        'Çanakkale' => 'Merkez',
        'Çankırı' => 'Merkez',
        'Çorum' => 'Merkez',
        'Denizli' => 'Pamukkale',
        'Diyarbakır' => 'Bağlar',
        'Düzce' => 'Merkez',
        'Edirne' => 'Merkez',
        'Elazığ' => 'Merkez',
        'Erzincan' => 'Merkez',
        'Erzurum' => 'Yakutiye',
        'Eskişehir' => 'Odunpazarı',
        'Gaziantep' => 'Şahinbey',
        'Giresun' => 'Merkez',
        'Gümüşhane' => 'Merkez',
        'Hakkari' => 'Merkez',
        'Hatay' => 'Antakya',
        'Iğdır' => 'Merkez',
        'Isparta' => 'Merkez',
        'İstanbul' => 'Kadıköy',
        'İzmir' => 'Konak',
        'Kahramanmaraş' => 'Dulkadiroğlu',
        'Karabük' => 'Merkez',
        'Karaman' => 'Merkez',
        'Kars' => 'Merkez',
        'Kastamonu' => 'Merkez',
        'Kayseri' => 'Melikgazi',
        'Kırıkkale' => 'Merkez',
        'Kırklareli' => 'Merkez',
        'Kırşehir' => 'Merkez',
        'Kilis' => 'Merkez',
        'Kocaeli' => 'İzmit',
        'Konya' => 'Selçuklu',
        'Kütahya' => 'Merkez',
        'Malatya' => 'Battalgazi',
        'Manisa' => 'Yunusemre',
        'Mardin' => 'Artuklu',
        'Mersin' => 'Akdeniz',
        'Muğla' => 'Menteşe',
        'Muş' => 'Merkez',
        'Nevşehir' => 'Merkez',
        'Niğde' => 'Merkez',
        'Ordu' => 'Altınordu',
        'Osmaniye' => 'Merkez',
        'Rize' => 'Merkez',
        'Sakarya' => 'Adapazarı',
        'Samsun' => 'İlkadım',
        'Siirt' => 'Merkez',
        'Sinop' => 'Merkez',
        'Sivas' => 'Merkez',
        'Şanlıurfa' => 'Eyyübiye',
        'Şırnak' => 'Merkez',
        'Tekirdağ' => 'Süleymanpaşa',
        'Tokat' => 'Merkez',
        'Trabzon' => 'Ortahisar',
        'Tunceli' => 'Merkez',
        'Uşak' => 'Merkez',
        'Van' => 'İpekyolu',
        'Yalova' => 'Merkez',
        'Yozgat' => 'Merkez',
        'Zonguldak' => 'Merkez',
    ];

    public function model(array $row)
    {
        // İl bulma - BÜYÜK/küçük harf düzeltmeli
        $province = null;
        if (!empty($row['il'])) {
            $provinceName = $this->normalizeName(trim($row['il']));
            $province = Province::whereRaw('LOWER(name) = ?', [mb_strtolower($provinceName)])->first();
        }

        // İlçe bulma - BÜYÜK/küçük harf düzeltmeli + Default ilçe mantığı
        $district = null;
        if ($province) {
            $districtName = null;

            // Eğer ilçe boşsa veya il adı ile aynıysa → Default ilçe kullan
            if (empty($row['ilce']) ||
                mb_strtolower(trim($row['ilce'])) === mb_strtolower($provinceName)) {
                $districtName = $this->defaultDistricts[$province->name] ?? 'Merkez';
            } else {
                $districtName = $this->normalizeName(trim($row['ilce']));
            }

            $district = District::whereRaw('LOWER(name) = ?', [mb_strtolower($districtName)])
                ->where('province_id', $province->id)
                ->first();

            // İlçe bulunamadıysa, o ilin ilk ilçesini al
            if (!$district) {
                $district = District::where('province_id', $province->id)->first();
            }
        }

        // Hala ilçe yoksa (il de bulunamadıysa), en küçük ID'li ilçeyi al
        if (!$district) {
            $district = District::orderBy('id')->first();
        }

        // Tehlike Sınıfı bulma - BÜYÜK/küçük harf düzeltmeli
        $dangerLevel = null;
        if (!empty($row['tehlike_sinifi'])) {
            $dangerLevelName = $this->normalizeName(trim($row['tehlike_sinifi']));
            $dangerLevel = DangerLevel::whereRaw('LOWER(name) = ?', [mb_strtolower($dangerLevelName)])->first();
        }

        return new CrmRecord([
            // Temel Bilgiler
            'file_number' => $row['dosya_no'] ?? null,
            'company_title' => $row['firma_unvani'] ?? null,
            'company_address' => !empty($row['firma_adresi']) ? $row['firma_adresi'] : 'Adres bilgisi girilecek',
            'service_address' => $row['hizmet_adresi'] ?? null,
            'province_id' => $province?->id ?? null,
            'district_id' => $district?->id ?? null,
            'neighborhood' => $row['mahalle'] ?? null,

            // Vergi & Resmi Bilgiler
            'tax_office' => $row['vergi_dairesi'] ?? null,
            'tax_number' => $row['vergi_no'] ?? null,
            'sgk_number' => $row['sgk_no'] ?? null,
            'trade_register_no' => $row['ticaret_sicil_no'] ?? null,
            'identity_no' => $row['tc_kimlik_no'] ?? null,

            // İletişim Bilgileri
            'officer_name' => $row['yetkili_adi'] ?? null,
            'phone' => $row['telefon'] ?? null,
            'email' => $row['e_posta'] ?? null,

            // İSG Bilgileri
            'personnel_count' => $row['personel_sayisi'] ?? null,
            'danger_level_id' => $dangerLevel?->id ?? null,
            'doctor_name' => $row['is_yeri_hekimi'] ?? null,
            'health_staff_name' => $row['saglik_personeli'] ?? null,
            'safety_expert_name' => $row['is_guvenligi_uzmani'] ?? null,
            'accountant_name' => $row['mali_musavir'] ?? null,

            // Sözleşme Bilgileri
            'contract_creator_name' => $row['sozlesmeyi_yapan'] ?? null,
            'contract_start' => $this->parseDate($row['sozlesme_baslangic'] ?? null),
            'contract_end' => $this->parseDate($row['sozlesme_bitis'] ?? null),
            'contract_months' => $row['sozlesme_suresi_ay'] ?? null,
            'monthly_price' => $this->parsePrice($row['aylik_ucret'] ?? null),
            'monthly_kdv' => $this->parsePrice($row['aylik_kdv'] ?? null),
            'monthly_total' => $this->parsePrice($row['aylik_toplam'] ?? null),

            // Randevu Bilgileri
            'appointment_date' => $this->parseDate($row['randevu_tarihi'] ?? null),
            'appointment_time' => $row['randevu_saati'] ?? null,

            // Diğer
            'notes' => $row['notlar'] ?? null,
        ]);
    }

    /**
     * İsim normalize et (Türkçe karakter + büyük/küçük harf duyarsız)
     */
    private function normalizeName($name)
    {
        // Baştaki/sondaki boşlukları temizle
        $name = trim($name);

        // İlk harfi büyük, geri kalanı küçük yap (Türkçe karakterlere uyumlu)
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }

    private function parseDate($date)
    {
        if (empty($date) || $date === '-') {
            return null;
        }

        try {
            // Excel'den gelen tarihi parse et (dd.mm.yyyy formatı)
            return Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parsePrice($price)
    {
        if (empty($price) || $price === '-') {
            return null;
        }

        // "1.234,56 ₺" -> 1234.56
        $price = str_replace(['.', '₺', ' '], '', $price);
        $price = str_replace(',', '.', $price);

        return (float) $price;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
