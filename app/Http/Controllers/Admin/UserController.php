<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // use AuthorizesRequests;

    public function index()
    {
        // $this->authorize('viewAny', User::class);
        $users = User::with(['roles.permissions', 'profile'])->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // $this->authorize('view', $user);
        $user->load(['roles.permissions', 'profile']);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        // $this->authorize('create', User::class);
        $roles = Role::all();
        $user = new User();
        return view('admin.users.create', compact('roles', 'user'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->validated() + [
            'password' => Hash::make($request->password),
        ]);
        Profile::create([
            'user_id' => $user->id
        ]);
        $role = $request->input('role', 'reader');
        $user->assignRole($role);
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $user)
    {
        // $this->authorize('update', $user);
        $user->load(['roles', 'profile']);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);
        $user->update($request->validated());
        $role = $request->input('role');
        if (is_array($role)) {
            return redirect()->back()->withErrors(['role' => 'Solo se puede asignar un rol a la vez']);
        }
        $user->syncRoles($role);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        // $this->authorize('destroy', $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
