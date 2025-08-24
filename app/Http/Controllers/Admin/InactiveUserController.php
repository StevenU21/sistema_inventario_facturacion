<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class InactiveUserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with(['roles.permissions', 'profile'])->where('is_active', false)->latest()->paginate(10);
        return view('admin.users.inactive', compact('users'));
    }

    public function reactivate($id)
    {
        $this->authorize('reactivate', User::class);
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $user->is_active = true;
        $user->save();
        return redirect()->route('users.inactive')->with('success', 'Usuario reactivado correctamente.');
    }
}
