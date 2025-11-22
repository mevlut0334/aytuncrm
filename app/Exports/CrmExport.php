<?php

namespace App\Exports;

use App\Models\CrmRecord;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CrmExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = CrmRecord::query()->with(['dangerLevel']);

        // Filtreleri uygula
        if (!empty($this->filters['file_number'])) {
            $query->where('file_number', 'like', '%' . $this->filters['file_number'] . '%');
        }

        if (!empty($this->filters['company_title'])) {
            $query->where('company_title', 'like', '%' . $this->filters['company_title'] . '%');
        }

        if (!empty($this->filters['danger_level'])) {
            $query->whereHas('dangerLevel', function ($q) {
                $q->where('name', 'like', '%' . $this->filters['danger_level'] . '%');
            });
        }

        if (!empty($this->filters['doctor_name'])) {
            $query->where('doctor_name', 'like', '%' . $this->filters['doctor_name'] . '%');
        }

        if (!empty($this->filters['health_staff_name'])) {
            $query->where('health_staff_name', 'like', '%' . $this->filters['health_staff_name'] . '%');
        }

        if (!empty($this->filters['safety_expert_name'])) {
            $query->where('safety_expert_name', 'like', '%' . $this->filters['safety_expert_name'] . '%');
        }

        if (!empty($this->filters['accountant_name'])) {
            $query->where('accountant_name', 'like', '%' . $this->filters['accountant_name'] . '%');
        }

        if (!empty($this->filters['contract_creator'])) {
            $query->where('contract_creator_name', 'like', '%' . $this->filters['contract_creator'] . '%');
        }

        return $query->orderBy('file_number');
    }

    public function headings(): array
    {
        return [
            'Dosya No',
            'Firma Unvanı',
            'Tehlike Sınıfı',
            'İş Yeri Hekimi',
            'Sağlık Personeli',
            'İş Güvenliği Uzmanı',
            'Mali Müşavir',
            'Sözleşmeyi Yapan',
            'Oluşturulma Tarihi',
        ];
    }

    public function map($record): array
    {
        return [
            $record->file_number,
            $record->company_title,
            $record->dangerLevel?->name ?? '-',
            $record->doctor_name ?? '-',
            $record->health_staff_name ?? '-',
            $record->safety_expert_name ?? '-',
            $record->accountant_name ?? '-',
            $record->contract_creator_name ?? '-',
            $record->created_at?->format('d.m.Y H:i') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50']
                ],
            ],
        ];
    }
}