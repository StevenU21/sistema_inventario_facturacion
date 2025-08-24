<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with(['roles.permissions', 'profile'])->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $user->load(['roles.permissions', 'profile']);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        $user = new User();
        return view('admin.users.create', compact('roles', 'user'));
    }

    public function store(UserRequest $request, ProfileRequest $profileRequest, FileService $fileService)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        $profileData = $profileRequest->validated();
        $profileData['user_id'] = $user->id;

        if ($profileRequest->hasFile('avatar')) {
            $profileData['avatar'] = $fileService->storeLocal($user, 'avatar', $profileRequest->file('avatar'), 'user_avatar');
        } else if ($profileRequest->filled('avatar')) {
            $profileData['avatar'] = $profileRequest->input('avatar');
        }

        // Eliminar nulos para evitar errores de mass assignment
        $profileData = array_filter($profileData, fn($v) => !is_null($v));
        Profile::create($profileData);

        $role = $request->input('role');
        $user->assignRole($role);
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $user->load(['roles', 'profile']);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);
        $data = $request->validated();
        // Si el password está vacío, no actualizarlo
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = \Hash::make($data['password']);
        }
        $user->update($data);

        // Actualizar datos de perfil y avatar
        if ($request->has(['phone', 'identity_card', 'gender', 'address']) || $request->hasFile('avatar')) {
            $profileData = $request->only(['phone', 'identity_card', 'gender', 'address']);
            $fileService = new FileService();
            if ($user->profile && $request->hasFile('avatar')) {
                // Actualizar avatar y eliminar el anterior
                $fileService->updateLocal($user->profile, 'avatar', $request->file('avatar'), 'user_avatar');
                $profileData['avatar'] = $user->profile->avatar;
            } else if ($request->hasFile('avatar')) {
                $profileData['avatar'] = $fileService->storeLocal($user, 'avatar', $request->file('avatar'), 'user_avatar');
            } else if ($request->filled('avatar')) {
                $profileData['avatar'] = $request->input('avatar');
            }
            // Eliminar nulos para evitar errores de mass assignment
            $profileData = array_filter($profileData, fn($v) => !is_null($v));
            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $profileData['user_id'] = $user->id;
                // Si hay archivo avatar en la request, procesar igual que en store
                if ($request->hasFile('avatar')) {
                    $profileData['avatar'] = $fileService->storeLocal($user, 'avatar', $request->file('avatar'), 'user_avatar');
                } else if ($request->filled('avatar')) {
                    $profileData['avatar'] = $request->input('avatar');
                }
                $profileData = array_filter($profileData, fn($v) => !is_null($v));
                Profile::create($profileData);
            }
        }

        $role = $request->input('role');
        if (is_array($role)) {
            return redirect()->back()->withErrors(['role' => 'Solo se puede asignar un rol a la vez']);
        }
        $user->syncRoles($role);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        // En vez de eliminar, desactivar el usuario
        $user->is_active = false;
        $user->save();
        return redirect()->route('users.index')->with('success', 'Usuario desactivado correctamente');
    }
}
