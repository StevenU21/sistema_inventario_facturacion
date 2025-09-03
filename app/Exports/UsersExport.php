<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = User::query()->with(['roles', 'profile']);

        // Filtros
        if (!empty($this->filters['status'])) {
            $isActive = $this->filters['status'] === 'activo' ? true : false;
            $query->where('is_active', $isActive);
        }
        if (!empty($this->filters['gender'])) {
            $query->whereHas('profile', function ($q) {
                $q->where('gender', $this->filters['gender']);
            });
        }
        if (!empty($this->filters['role'])) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filters['role']);
            });
        }
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('profile', function ($q2) use ($search) {
                        $q2->where('identity_card', 'like', "%$search%")
                            ->orWhere('phone', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%")
                            ->orWhere('gender', 'like', "%$search%")
                        ;
                    });
            });
        }

        $users = $query->get();

        // Mapear los datos exportados
        return $users->map(function ($user) {
            $profile = $user->profile;
            $role = $user->roles->pluck('name')->first();
            return [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'identity_card' => $profile ? ($profile->formatted_identity_card ?? $profile->identity_card) : null,
                'phone' => $profile ? $profile->phone : null,
                'gender' => $profile ? $profile->gender : null,
                'role' => $role ? mb_strtoupper($role) : null,
                'address' => $profile ? $profile->address : null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Cédula',
            'Teléfono',
            'Género',
            'Rol',
            'Dirección',
        ];
    }
}
