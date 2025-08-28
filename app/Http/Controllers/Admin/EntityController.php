<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Models\Entity;
use App\Models\Municipality;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntityRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EntityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Entity::class);
        $entities = Entity::latest()->paginate(10);
        return view('admin.entities.index', compact('entities'));
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
        return redirect()->route('entities.index')->with('success', 'Entidad actualizada correctamente.');
    }

    public function destroy(Entity $entity)
    {
        $this->authorize('destroy', $entity);
        $entity->delete();
        return redirect()->route('entities.index')->with('success', 'Entidad eliminada correctamente.');
    }
}
