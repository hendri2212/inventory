<?php

namespace App\Policies;

use App\Models\User;
use App\Support\InventoryAccess;
use Illuminate\Database\Eloquent\Model;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasValidAssignment();
    }

    public function view(User $user, Model $model): bool
    {
        return app(InventoryAccess::class)->canRead($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->hasValidAssignment() && $user->role !== 'peminjam';
    }

    public function update(User $user, Model $model): bool
    {
        return $this->view($user, $model) && app(InventoryAccess::class)->canWrite($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->update($user, $model);
    }
}
