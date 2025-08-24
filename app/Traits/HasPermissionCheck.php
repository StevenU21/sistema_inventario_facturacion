<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Exceptions\UnauthorizedException;

trait HasPermissionCheck
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }


    public function checkPermission(User $user, string $permission, ?Model $model = null): bool
    {
        if (!$user->hasPermissionTo($permission)) {
            throw new UnauthorizedException(403);
        }

        if ($model && $user->id !== $model->user_id) {
            throw new UnauthorizedException(403);
        }

        return true;
    }
}
