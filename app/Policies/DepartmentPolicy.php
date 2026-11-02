<?php

namespace App\Policies;

use App\Models\User;

class DepartmentPolicy extends InventoryPolicy
{
    public function create(User $user): bool
    {
        return $user->hasValidAssignment() && in_array($user->role, ['superadmin', 'admin'], true);
    }
}
