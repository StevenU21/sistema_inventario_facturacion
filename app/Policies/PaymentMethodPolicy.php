<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentMethodPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read payment_methods');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read payment_methods');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create payment_methods');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update payment_methods');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy payment_methods');
    }
}
