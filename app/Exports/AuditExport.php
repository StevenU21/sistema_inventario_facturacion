<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Classes\AuditPresenter;

class AuditExport implements FromCollection, WithHeadings
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $activities = $this->query->get();
        return $activities->map(function ($activity) {
            return AuditPresenter::present($activity);
        });
    }

    public function headings(): array
    {
        return ['ID', 'Fecha', 'Usuario', 'Evento', 'Modelo', 'ID Modelo', 'Antes', 'Después'];
    }

}
