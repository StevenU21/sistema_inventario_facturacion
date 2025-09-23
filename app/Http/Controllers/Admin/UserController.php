<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Services\ModelSearchService;
use App\Models\Profile;
use App\Services\FileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Carbon\Carbon;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with(['roles', 'profile'])->where('is_active', true)->latest()->paginate(10);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function search()
    {
        $this->authorize('viewAny', User::class);
        $service = new ModelSearchService();
        $params = [
            'search' => request('search'),
            'per_page' => request('per_page', 10),
            'role' => request('role'),
            // Si no se especifica status, por defecto 'activo', pero si es vacío mostrar todos
            'status' => request()->has('status') ? request('status') : 'activo',
            'gender' => request('gender'),
        ];
        $users = $service->search(
            User::class,
            $params,
            [
                'first_name',
                'last_name',
                'email',
                'profile.identity_card',
                'roles.name',
                'roles.display_name',
                'roles.description',
            ],
            ['roles', 'profile'],
            function ($query, $params) {
                // Estado: activo/inactivo
                if (isset($params['status']) && $params['status'] !== null && $params['status'] !== '') {
                    if ($params['status'] === 'activo') {
                        $query->where('is_active', true);
                    } elseif ($params['status'] === 'inactivo') {
                        $query->where('is_active', false);
                    }
                }
                // Género
                if (isset($params['gender']) && $params['gender']) {
                    $query->whereHas('profile', function ($q) use ($params) {
                        $q->where('gender', $params['gender']);
                    });
                }
                // Rol
                if (isset($params['role']) && $params['role']) {
                    $query->whereHas('roles', function ($q) use ($params) {
                        $q->where('name', $params['role']);
                    });
                }

                // Ordenamiento
                $sort = request('sort', 'id');
                $direction = request('direction', 'desc');
                $allowedSorts = [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'is_active',
                    'created_at',
                    'role',
                ];
                if (in_array($sort, $allowedSorts)) {
                    if ($sort === 'role') {
                        // Ordenar por el primer rol (alfabético)
                        $query->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                            ->orderBy('roles.name', $direction)
                            ->select('users.*');
                    } else {
                        $query->orderBy($sort, $direction);
                    }
                } else {
                    $query->latest();
                }
            }
        );
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function export()
    {
        $this->authorize('viewAny', User::class);
        $filters = [
            'search' => request('search'),
            'role' => request('role'),
            'status' => request('status'),
            'gender' => request('gender'),
        ];
        $now = Carbon::now()->format('Ymd_His');
        $filename = "users_{$now}.xlsx";
        return Excel::download(new UsersExport($filters), $filename);
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
            $profileData['avatar'] = $fileService->storeLocal($user, $profileRequest->file('avatar'));
        } elseif ($profileRequest->filled('avatar')) {
            $profileData['avatar'] = $profileRequest->input('avatar');
        }
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
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = \Hash::make($data['password']);
        }
        $user->update($data);

        // Actualizar datos de perfil y avatar
        $profileData = $request->only(['phone', 'identity_card', 'gender', 'address']);
        $fileService = new FileService();
        // Procesar avatar subido
        if ($request->hasFile('avatar')) {
            if ($user->profile) {
                $avatarPath = $fileService->updateLocal($user->profile, 'avatar', $request);
            } else {
                $avatarPath = $fileService->storeLocal($user, $request->file('avatar'));
            }
            if (!empty($avatarPath)) {
                $profileData['avatar'] = $avatarPath;
            }
        } elseif ($request->filled('avatar')) {
            $profileData['avatar'] = $request->input('avatar');
        }
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $profileData['user_id'] = $user->id;
            Profile::create($profileData);
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
        if ($user->is_active) {
            $user->is_active = false;
            $user->save();
            return redirect()->route('users.index')->with('deleted', 'Usuario desactivado correctamente');
        } else {
            $this->authorize('destroy', $user);
            $user->is_active = true;
            $user->save();
            return redirect()->route('users.index')->with('updated', 'Usuario reactivado correctamente.');
        }
    }
}
