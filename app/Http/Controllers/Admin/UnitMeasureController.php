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
        $unitMeasures = UnitMeasure::latest()->paginate(10);
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
