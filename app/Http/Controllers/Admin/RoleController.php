<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __invoke()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::orderBy('id', 'asc')->pluck('name', 'id');

        return view('admin.roles.index', compact('roles'));
    }
}
