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
        $activities = $this->query->with('subject')->get();
        return $activities->map(function ($activity) {
            $presented = AuditPresenter::present($activity);
            // Nombre del modelo relacionado si existe: name, title, o first_name+last_name
            $modelDisplay = '-';
            if ($activity->subject) {
                if (isset($activity->subject->name)) {
                    $modelDisplay = $activity->subject->name;
                } elseif (isset($activity->subject->title)) {
                    $modelDisplay = $activity->subject->title;
                } elseif (isset($activity->subject->first_name) || isset($activity->subject->last_name)) {
                    $modelDisplay = trim(($activity->subject->first_name ?? '') . ' ' . ($activity->subject->last_name ?? ''));
                } else {
                    $modelDisplay = $activity->subject_id ?? '-';
                }
            } else {
                $modelDisplay = $activity->subject_id ?? '-';
            }
            // Insertar el campo después de 'Modelo'
            $row = [
                $presented['ID'],
                $presented['Fecha'],
                $presented['Usuario'],
                $presented['Evento'],
                $presented['Registro'],
                $modelDisplay,
                $presented['Antes'],
                $presented['Después'],
            ];
            return $row;
        });
    }

    public function headings(): array
    {
        return ['ID', 'Fecha', 'Usuario', 'Evento', 'Registro', 'Nombre Registro', 'Antes', 'Después'];
    }
}
