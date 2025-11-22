<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCrmRecordRequest;
use App\Http\Requests\UpdateCrmRecordRequest;
use App\Services\Interfaces\CrmRecordServiceInterface;
use App\Models\DangerLevel;
use App\Models\Personnel;
use App\Models\Province;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\CrmExport;
use Maatwebsite\Excel\Facades\Excel;

class CrmRecordController extends Controller
{
    public function __construct(
        protected CrmRecordServiceInterface $crmRecordService
    ) {}

    /**
     * Excel Export - Sadece Admin
     */
    public function export(Request $request)
    {
        // Admin kontrolü
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Filtreleri al
        $filters = $request->only([
            'file_number',
            'company_title',
            'danger_level',
            'doctor_name',
            'health_staff_name',
            'safety_expert_name',
            'accountant_name',
            'contract_creator'
        ]);

        // Dosya adı oluştur
        $hasFilters = collect($filters)->filter()->isNotEmpty();
        $fileName = $hasFilters 
            ? 'firmalar_filtreli_' . date('Y-m-d_His') . '.xlsx'
            : 'firmalar_tumu_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new CrmExport($filters), $fileName);
    }

    /**
     * Firma listesi (Dashboard görevi görecek) - 8 FİLTRE DESTEĞİ
     */
    public function index(Request $request)
    {
        try {
            // ✅ 8 Text Input Filtresi
            $filters = $request->only([
                'file_number',           // 1. Dosya Numarası
                'company_title',         // 2. Firma Unvanı
                'danger_level',          // 3. Tehlike Sınıfı
                'doctor_name',           // 4. İş Yeri Hekimi
                'health_staff_name',     // 5. Sağlık Personeli
                'safety_expert_name',    // 6. İş Güvenliği Uzmanı
                'accountant_name',       // 7. Mali Müşavir
                'contract_creator',      // 8. Sözleşmeyi Yapan
                
                // Eski ID bazlı filtreler (opsiyonel - backward compatibility)
                'danger_level_id',
                'doctor_id',
                'health_staff_id',
                'safety_expert_id',
                'accountant_id',
                'contract_creator_id'
            ]);

            // Service üzerinden veri çek (paginated)
            $records = $this->crmRecordService->index($filters, 20);

            // Filtreleme için gerekli datalar (dropdown'lar için - şimdilik gerekli değil ama bıraktık)
            $dangerLevels = DangerLevel::select('id', 'name')->get();
            $doctors = Personnel::where('role', 'doctor')->select('id', 'name')->get();
            $healthStaff = Personnel::where('role', 'health_staff')->select('id', 'name')->get();
            $safetyExperts = Personnel::where('role', 'safety_expert')->select('id', 'name')->get();
            $accountants = Personnel::where('role', 'accountant')->select('id', 'name')->get();
            $contractCreators = User::select('id', 'name')->get();

            return view('crm.index', compact(
                'records',
                'dangerLevels',
                'doctors',
                'healthStaff',
                'safetyExperts',
                'accountants',
                'contractCreators'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Firmalar listelenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Yeni firma ekleme formu
     */
    public function create()
    {
        try {
            // Form için gerekli datalar
            $provinces = Province::select('id', 'name', 'plate_code')->orderBy('name')->get();
            $districts = collect(); // Boş collection - JS ile doldurulacak
            $dangerLevels = DangerLevel::select('id', 'name')->get();
            $doctors = Personnel::where('role', 'doctor')->select('id', 'name', 'phone')->get();
            $healthStaff = Personnel::where('role', 'health_staff')->select('id', 'name', 'phone')->get();
            $safetyExperts = Personnel::where('role', 'safety_expert')->select('id', 'name', 'phone')->get();
            $accountants = Personnel::where('role', 'accountant')->select('id', 'name', 'phone')->get();

            return view('crm.create', compact(
                'provinces',
                'districts',
                'dangerLevels',
                'doctors',
                'healthStaff',
                'safetyExperts',
                'accountants'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Yeni firma kaydet
     */
    public function store(StoreCrmRecordRequest $request)
    {
        try {
            $this->crmRecordService->store($request->validated());

            return redirect()
                ->route('crm.index')
                ->with('success', 'Firma başarıyla eklendi.');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Firma eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Firma detay sayfası
     */
    public function show(int $id)
    {
        try {
            $record = $this->crmRecordService->show($id);

            if (!$record) {
                return redirect()
                    ->route('crm.index')
                    ->with('error', 'Firma bulunamadı.');
            }

            return view('crm.show', compact('record'));
        } catch (\Exception $e) {
            return back()->with('error', 'Firma detayları yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Firma düzenleme formu
     */
    public function edit(int $id)
    {
        try {
            $record = $this->crmRecordService->show($id);

            if (!$record) {
                return redirect()
                    ->route('crm.index')
                    ->with('error', 'Firma bulunamadı.');
            }

            // Form için gerekli datalar
            $provinces = Province::select('id', 'name', 'plate_code')->orderBy('name')->get();
            
            // Seçili ilin ilçelerini getir
            $districts = District::where('province_id', $record->province_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
                
            $dangerLevels = DangerLevel::select('id', 'name')->get();
            $doctors = Personnel::where('role', 'doctor')->select('id', 'name', 'phone')->get();
            $healthStaff = Personnel::where('role', 'health_staff')->select('id', 'name', 'phone')->get();
            $safetyExperts = Personnel::where('role', 'safety_expert')->select('id', 'name', 'phone')->get();
            $accountants = Personnel::where('role', 'accountant')->select('id', 'name', 'phone')->get();

            return view('crm.edit', compact(
                'record',
                'provinces',
                'districts',
                'dangerLevels',
                'doctors',
                'healthStaff',
                'safetyExperts',
                'accountants'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Düzenleme formu yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Firma güncelle
     */
    public function update(UpdateCrmRecordRequest $request, int $id)
    {
        try {
            $this->crmRecordService->update($id, $request->validated());

            return redirect()
                ->route('crm.show', $id)
                ->with('success', 'Firma bilgileri güncellendi.');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Firma güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Firma sil
     */
    public function destroy(int $id)
    {
        try {
            $this->crmRecordService->delete($id);

            return redirect()
                ->route('crm.index')
                ->with('success', 'Firma başarıyla silindi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Firma silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * İl seçilince ilçeleri getir (AJAX)
     */
    public function getDistricts(int $province_id)
    {
        try {
            $districts = District::where('province_id', $province_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($districts);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlçeler yüklenirken bir hata oluştu.'
            ], 500);
        }
    }
}