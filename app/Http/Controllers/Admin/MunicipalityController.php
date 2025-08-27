<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MunicipalityRequest;
use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MunicipalityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Municipality::class);
        $municipalities = Municipality::with('department')->latest()->paginate(10);
        return view('admin.municipalities.index', compact('municipalities'));
    }

    public function create()
    {
        $this->authorize('create', Municipality::class);
        $departments = Department::pluck('name', 'id');
        return view('admin.municipalities.create', compact('departments'));
    }

    public function store(MunicipalityRequest $request)
    {
        Municipality::create($request->validated());
        return redirect()->route('municipalities.index')->with('success', 'Municipio creado correctamente.');
    }

    public function show(Municipality $municipality)
    {
        $this->authorize('view', $municipality);
        $municipality->load('department');
        return view('admin.municipalities.show', compact('municipality'));
    }

    public function edit(Municipality $municipality)
    {
        $this->authorize('update', $municipality);
        $departments = Department::pluck('name', 'id');
        return view('admin.municipalities.edit', compact('municipality', 'departments'));
    }

    public function update(MunicipalityRequest $request, Municipality $municipality)
    {
        $municipality->update($request->validated());
        return redirect()->route('municipalities.index')->with('success', 'Municipio actualizado correctamente.');
    }

    public function destroy(Municipality $municipality)
    {
        $this->authorize('destroy', $municipality);
        $municipality->delete();
        return redirect()->route('municipalities.index')->with('success', 'Municipio eliminado correctamente.');
    }
}
