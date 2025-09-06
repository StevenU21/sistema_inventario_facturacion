<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WarehousesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Dirección',
            'Descripción',
            'Activo',
            'Fecha creación',
            'Fecha actualización',
        ];
    }

    public function map($warehouse): array
    {
        return [
            $warehouse->id,
            $warehouse->name,
            $warehouse->address,
            $warehouse->description,
            $warehouse->is_active ? 'Sí' : 'No',
            $warehouse->created_at,
            $warehouse->updated_at,
        ];
    }
}
