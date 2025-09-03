<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ModelSearchService
{
    /**
     * Aplica filtros de búsqueda y paginación a la consulta de cualquier modelo.
     *
     * @param class-string<Model> $modelClass
     * @param array $params
     * @param array $searchFields
     * @param array $withRelations
     * @param callable|null $customQueryCallback
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(
        string $modelClass,
        array $params = [],
        array $searchFields = [],
        array $withRelations = [],
        callable $customQueryCallback = null
    ) {
        $query = $modelClass::query();
        if (!empty($withRelations)) {
            $query->with($withRelations);
        }
        // Si hay callback personalizado, se ejecuta
        if ($customQueryCallback) {
            $customQueryCallback($query, $params);
        }
        // Filtro de búsqueda genérico
        if (!empty($params['search']) && !empty($searchFields)) {
            $search = $params['search'];
            $query->where(function ($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    if (str_contains($field, '.')) {
                        // Relación
                        [$relation, $relField] = explode('.', $field, 2);
                        $q->orWhereHas($relation, function ($qr) use ($relField, $search) {
                            $qr->where($relField, 'like', "%$search%");
                        });
                    } else {
                        $q->orWhere($field, 'like', "%$search%");
                    }
                }
            });
        }
        $perPage = $params['per_page'] ?? 10;
        return $query->latest()->paginate($perPage)->withQueryString();
    }
}
