<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitMeasure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UnitMeasureRequest;

class UnitMeasureController extends Controller
{
    use AuthorizesRequests;


    public function index()
    {
        $this->authorize('viewAny', UnitMeasure::class);
        $perPage = request('per_page', 10);
        $unitMeasures = UnitMeasure::latest()->paginate($perPage);
        return view('admin.unit_measures.index', compact('unitMeasures'));
    }

    public function search()
    {
        $this->authorize('viewAny', UnitMeasure::class);
        $query = UnitMeasure::query();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('abbreviation', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if (request('name')) {
            $query->where('name', request('name'));
        }

        // Ordenamiento
        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
    $allowedSorts = ['id', 'name', 'abbreviation', 'description', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = request('per_page', 10);
        $unitMeasures = $query->paginate($perPage)->withQueryString();
        return view('admin.unit_measures.index', compact('unitMeasures'));
    }

    public function create()
    {
        $this->authorize('create', UnitMeasure::class);
        return view('admin.unit_measures.create');
    }

    public function store(UnitMeasureRequest $request)
    {
        $data = $request->validated();
        UnitMeasure::create($data);
        return redirect()->route('unit_measures.index')->with('success', 'Unidad de medida creada correctamente.');
    }

    public function show(UnitMeasure $unitMeasure)
    {
        $this->authorize('view', $unitMeasure);
        return view('admin.unit_measures.show', compact('unitMeasure'));
    }

    public function edit(UnitMeasure $unitMeasure)
    {
        $this->authorize('update', $unitMeasure);
        return view('admin.unit_measures.edit', compact('unitMeasure'));
    }

    public function update(UnitMeasureRequest $request, UnitMeasure $unitMeasure)
    {
        $data = $request->validated();
        $unitMeasure->update($data);
        return redirect()->route('unit_measures.index')->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnitMeasure $unitMeasure)
    {
        $this->authorize('destroy', $unitMeasure);
        $unitMeasure->delete();
        return redirect()->route('unit_measures.index')->with('success', 'Unidad de medida eliminada correctamente.');
    }
}
