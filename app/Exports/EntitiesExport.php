<?php

namespace App\Exports;

use App\Models\Entity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntitiesExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Entity::with('municipality');
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('identity_card', 'like', "%$search%")
                    ->orWhere('ruc', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }
        if (array_key_exists('is_client', $this->filters) && $this->filters['is_client'] !== '') {
            $query->where('is_client', (bool) $this->filters['is_client']);
        }
        if (array_key_exists('is_supplier', $this->filters) && $this->filters['is_supplier'] !== '') {
            $query->where('is_supplier', (bool) $this->filters['is_supplier']);
        }
        if (array_key_exists('is_active', $this->filters) && $this->filters['is_active'] !== '') {
            $query->where('is_active', (bool) $this->filters['is_active']);
        }
        if (!empty($this->filters['municipality_id'])) {
            $query->where('municipality_id', $this->filters['municipality_id']);
        }

        $entities = $query->get();

        return $entities->map(function ($e) {
            return [
                'id' => $e->id,
                'first_name' => $e->first_name,
                'last_name' => $e->last_name,
                'identity_card' => $e->formatted_identity_card ?? $e->identity_card,
                'ruc' => $e->ruc,
                'email' => $e->email,
                'phone' => $e->formatted_phone ?? $e->phone,
                'municipality' => $e->municipality->name ?? '-',
                'is_client' => $e->is_client ? 'SÍ' : 'NO',
                'is_supplier' => $e->is_supplier ? 'SÍ' : 'NO',
                'is_active' => $e->is_active ? 'SÍ' : 'NO',
                'created_at' => $e->created_at,
                'updated_at' => $e->updated_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombres',
            'Apellidos',
            'Cédula',
            'RUC',
            'Email',
            'Teléfono',
            'Municipio',
            'Cliente',
            'Proveedor',
            'Activo',
            'Fecha creación',
            'Fecha actualización',
        ];
    }
}
