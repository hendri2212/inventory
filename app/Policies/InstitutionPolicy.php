<?php

namespace App\Policies;

use App\Models\User;

class InstitutionPolicy extends InventoryPolicy
{
    public function create(User $user): bool
    {
        return $user->hasValidAssignment() && $user->role === 'superadmin';
    }
}
