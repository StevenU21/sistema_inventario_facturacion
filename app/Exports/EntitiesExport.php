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
        // Normalize filters to ignore empty strings/nulls
        $filters = array_filter($this->filters, function ($v) {
            return !is_null($v) && $v !== '';
        });

        // Filtrar entidades según permisos de lectura de cliente/proveedor
        $user = auth()->user();
        $query = Entity::with('municipality')
            ->visibleFor($user);
        if (!empty($filters['search'])) {
            $search = $filters['search'];
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
        if (array_key_exists('is_client', $filters)) {
            $query->where('is_client', (bool) $filters['is_client']);
        }
        if (array_key_exists('is_supplier', $filters)) {
            $query->where('is_supplier', (bool) $filters['is_supplier']);
        }
        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
        if (!empty($filters['municipality_id'])) {
            $query->where('municipality_id', $filters['municipality_id']);
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
