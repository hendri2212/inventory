<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasValidAssignment() && in_array($user->role, ['superadmin', 'admin'], true);
    }

    public function view(User $user, User $target): bool
    {
        return $this->viewAny($user) && User::query()->visibleTo($user)->whereKey($target->id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $target): bool
    {
        return $this->view($user, $target)
            && ($user->role === 'superadmin' || in_array($target->role, ['operator'], true));
    }

    public function delete(User $user, User $target): bool
    {
        return $user->id !== $target->id && $this->update($user, $target);
    }
}
