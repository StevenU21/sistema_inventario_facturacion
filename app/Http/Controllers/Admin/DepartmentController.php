<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DepartmentController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Department::class);
        $departments = Department::latest()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create', Department::class);
        return view('admin.departments.create');
    }

    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated());
        return redirect()->route('departments.index')->with('success', 'Departamento creado correctamente.');
    }

    public function show(Department $department)
    {
        $this->authorize('view', $department);
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        return view('admin.departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());
        return redirect()->route('departments.index')->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Department $department)
    {
        $this->authorize('destroy', $department);
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departamento eliminado correctamente.');
    }
}
