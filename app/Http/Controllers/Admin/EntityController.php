<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EntitiesExport;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Municipality;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntityRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Entity::class);
        $perPage = request('per_page', 10);
        $entities = Entity::with('municipality')->latest()->paginate($perPage);
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $municipalities = Municipality::orderBy('name')->pluck('name', 'id');
        $departmentsByMunicipality = Municipality::pluck('department_id', 'id');
        return view('admin.entities.index', compact('entities', 'departments', 'municipalities', 'departmentsByMunicipality'));
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Entity::class);
        $query = Entity::with('municipality');
        // Filtros básicos
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $tokens = array_values(array_filter(preg_split('/\s+/', $search)));
            $driver = DB::getDriverName();
            $collation = 'utf8mb4_unicode_ci';

            $query->where(function ($q) use ($tokens, $driver, $collation) {
                if (empty($tokens)) return;
                foreach ($tokens as $token) {
                    $like = "%$token%";
                    $q->where(function ($sub) use ($like, $driver, $collation) {
                        if ($driver === 'mysql') {
                            $sub->whereRaw("first_name COLLATE $collation LIKE ?", [$like])
                                ->orWhereRaw("last_name COLLATE $collation LIKE ?", [$like]);
                        } else {
                            $sub->where('first_name', 'like', $like)
                                ->orWhere('last_name', 'like', $like);
                        }
                    });
                }
            });
        }
        if ($request->filled('is_client')) {
            $query->where('is_client', (bool) $request->boolean('is_client'));
        }
        if ($request->filled('is_supplier')) {
            $query->where('is_supplier', (bool) $request->boolean('is_supplier'));
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->boolean('is_active'));
        }
        if ($request->filled('municipality_id')) {
            $query->where('municipality_id', $request->input('municipality_id'));
        }

        // Ordenamiento por <th>
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'first_name', 'last_name', 'identity_card', 'ruc', 'email', 'phone', 'municipality_id', 'is_client', 'is_supplier', 'is_active', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);
        $entities = $query->paginate($perPage)->appends($request->all());

        $departments = Department::orderBy('name')->pluck('name', 'id');
        $municipalities = Municipality::orderBy('name')->pluck('name', 'id');
        $departmentsByMunicipality = Municipality::pluck('department_id', 'id');
        return view('admin.entities.index', compact('entities', 'departments', 'municipalities', 'departmentsByMunicipality'));
    }

    // Endpoint para autocompletar entidades por nombre
    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', Entity::class);
        $term = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(20, $limit));

        $query = Entity::query();
        if ($term !== '') {
            $tokens = array_values(array_filter(preg_split('/\s+/', $term)));
            $driver = DB::getDriverName();
            $collation = 'utf8mb4_unicode_ci';
            $query->where(function ($q) use ($tokens, $driver, $collation) {
                foreach ($tokens as $token) {
                    $like = "%$token%";
                    $q->where(function ($sub) use ($like, $driver, $collation) {
                        if ($driver === 'mysql') {
                            $sub->whereRaw("first_name COLLATE $collation LIKE ?", [$like])
                                ->orWhereRaw("last_name COLLATE $collation LIKE ?", [$like]);
                        } else {
                            $sub->where('first_name', 'like', $like)
                                ->orWhere('last_name', 'like', $like);
                        }
                    });
                }
            });
        }

        $entities = $query->select(['id', 'first_name', 'last_name'])
            ->orderBy('first_name')
            ->limit($limit)
            ->get();

        $suggestions = $entities->map(function ($e) {
            $full = trim($e->first_name . ' ' . $e->last_name);
            return [
                'id' => $e->id,
                'text' => $full,
            ];
        });

        return response()->json([
            'data' => $suggestions,
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Entity::class);
        $filters = $request->only(['search', 'is_client', 'is_supplier', 'is_active', 'municipality_id']);
        // Remove empty/null values so we don't apply unintended filters
        $filters = array_filter($filters, function ($v) {
            return !is_null($v) && $v !== '';
        });
        $timestamp = now()->format('Ymd_His');
        $filename = "entidades_filtradas_{$timestamp}.xlsx";
        return Excel::download(new EntitiesExport($filters), $filename);
    }

    public function create()
    {
        $this->authorize('create', Entity::class);
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $municipalities = Municipality::orderBy('name')->pluck('name', 'id');
        $departmentsByMunicipality = Municipality::pluck('department_id', 'id');

        return view('admin.entities.create', compact('departments', 'municipalities', 'departmentsByMunicipality'));
    }

    public function store(EntityRequest $request)
    {
        $this->authorize('create', Entity::class);
        Entity::create($request->validated());
        return redirect()->route('entities.index')->with('success', 'Entidad creada correctamente.');
    }

    public function show(Entity $entity)
    {
        $this->authorize('view', $entity);
        return view('admin.entities.show', compact('entity'));
    }

    public function edit(Entity $entity)
    {
        $this->authorize('update', $entity);
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $municipalities = Municipality::orderBy('name')->pluck('name', 'id');
        $departmentsByMunicipality = Municipality::pluck('department_id', 'id');

        return view('admin.entities.edit', compact('entity', 'departments', 'municipalities', 'departmentsByMunicipality'));
    }

    public function update(EntityRequest $request, Entity $entity)
    {
        $this->authorize('update', $entity);
        $entity->update($request->validated());
        return redirect()->route('entities.index')->with('updated', 'Entidad actualizada correctamente.');
    }

    public function destroy(Entity $entity)
    {
        $this->authorize('destroy', $entity);

        if ($entity->is_active) {
            $entity->is_active = false;
            $entity->save();
            return redirect()->route('entities.index')->with('deleted', 'Entidad deshabilitada correctamente.');
        } else {
            $entity->is_active = true;
            $entity->save();
            return redirect()->route('entities.index')->with('success', 'Entidad habilitada correctamente.');
        }
    }
}
