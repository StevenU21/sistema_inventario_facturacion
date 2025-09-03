<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InactiveUserController extends Controller
{
    use AuthorizesRequests;

    public function reactivate($id)
    {
        $this->authorize('reactivate', User::class);
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $user->is_active = true;
        $user->save();
        return redirect()->route('users.index')->with('success', 'Usuario reactivado correctamente.');
    }
}
