<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // use AuthorizesRequests;

    // Listar permisos (solo vista)
    public function index()
    {
        // $this->authorize('viewAny', Permission::class);
        $permissions = Permission::orderBy('id', 'asc')->paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }

    // Mostrar un permiso específico (solo vista)
    public function show(Permission $permission)
    {
        // $this->authorize('view', $permission);
        return view('admin.permissions.show', compact('permission'));
    }

    // Formulario para crear permiso
    public function create()
    {
        // $this->authorize('create', Permission::class);
        return view('admin.permissions.create');
    }

    // Guardar nuevo permiso
    public function store(Request $request)
    {
        // $this->authorize('create', Permission::class);
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);
        $permission = Permission::create($data);
        return redirect()->route('permissions.index')->with('success', 'Permiso creado correctamente.');
    }

    // Formulario para editar permiso
    public function edit(Permission $permission)
    {
        // $this->authorize('update', $permission);
        return view('admin.permissions.edit', compact('permission'));
    }

    // Actualizar permiso
    public function update(Request $request, Permission $permission)
    {
        // $this->authorize('update', $permission);
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);
        $permission->update($data);
        return redirect()->route('permissions.index')->with('success', 'Permiso actualizado correctamente.');
    }

    // Eliminar permiso
    public function destroy(Permission $permission)
    {
        // $this->authorize('delete', $permission);
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permiso eliminado correctamente.');
    }

    // Obtener permisos de usuario (solo vista)
    public function getUserPermissions(User $user)
    {
        // $this->authorize('view', $user);
        $directPermissions = $user->getDirectPermissions()->pluck('name');
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');
        return view('admin.permissions.user-permissions', compact('user', 'directPermissions', 'rolePermissions'));
    }

    // Asignar permisos a usuario (solo web)
    public function assignPermission(Request $request, User $user)
    {
        // $this->authorize('assignPermissions', Permission::class);
        $request->validate([
            'permission' => ['required', 'array'],
            'permission.*' => ['exists:permissions,name'],
        ]);
        $permissions = $request->input('permission');
        $user->givePermissionTo($permissions);
        return back()->with('success', 'Permisos asignados correctamente.');
    }

    // Revocar permisos a usuario (solo web)
    public function revokePermission(Request $request, User $user)
    {
        // $this->authorize('revokePermissions', Permission::class);
        $request->validate([
            'permission' => ['required', 'array'],
            'permission.*' => ['exists:permissions,name'],
        ]);
        $permissions = $request->input('permission');
        $revokedPermissions = [];
        $rolePermissions = [];
        foreach ($permissions as $permission) {
            if ($user->hasDirectPermission($permission)) {
                $user->revokePermissionTo($permission);
                $revokedPermissions[] = $permission;
            } else {
                $rolePermissions[] = $permission;
            }
        }
        $message = 'Permisos revocados correctamente';
        if (!empty($rolePermissions)) {
            $message .= '. Los siguientes permisos son heredados de roles y no pueden ser revocados: ' . implode(', ', $rolePermissions);
        }
        return back()->with('success', $message);
    }
}
