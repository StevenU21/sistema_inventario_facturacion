<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function edit(User $user)
    {
        $this->authorize('view', Permission::class);
        $directPermissions = $user->getDirectPermissions()->pluck('name');
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');
        $allPermissions = Permission::all()->pluck('name');
        $specialPermissions = $allPermissions->diff($rolePermissions);
        $displayPermissions = $specialPermissions;
        return view('admin.permissions.edit', compact('user', 'directPermissions', 'rolePermissions', 'displayPermissions'));
    }

    public function assignPermission(Request $request, User $user)
    {
        $this->authorize('create', Permission::class);
        $request->validate([
            'permission' => ['nullable', 'array'],
            'permission.*' => ['exists:permissions,name'],
        ]);
        $permissions = $request->input('permission', []);
        // Solo sincroniza los permisos directos, sin tocar los heredados por roles
        $user->syncPermissions($permissions);
        return back()->with('success', 'Permisos actualizados correctamente.');
    }

    public function revokePermission(Request $request, User $user)
    {
        $this->authorize('update', Permission::class);
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
