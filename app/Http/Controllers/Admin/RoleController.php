<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Role::class);
        $roles = Role::latest()->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create($request->validated());
        $permissionIds = $request->input('permissions');
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        $role->syncPermissions($permissionNames);

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);
        $permissions = $role->permissions;
        return view('admin.roles.show', compact('role', 'permissions'));
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        $permissionIds = $request->input('permissions');
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        $role->syncPermissions($permissionNames);
        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('destroy', $role);
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}
