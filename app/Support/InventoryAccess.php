<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InventoryAccess
{
    /**
     * @template T of Model
     *
     * @param  Builder<T>  $query
     */
    public function constrain(Builder $query, User $user): void
    {
        if ($user->role === 'superadmin') {
            return;
        }
        $model = $query->getModel();
        $column = $model instanceof Institution ? 'id' : 'institution_id';
        $query->where($model->qualifyColumn($column), $user->institution_id);
        if (in_array($user->role, ['operator', 'peminjam'], true)) {
            if ($model instanceof Department) {
                $query->where($model->qualifyColumn('id'), $user->department_id);
            } elseif ($model instanceof Item) {
                $query->where($model->qualifyColumn('department_id'), $user->department_id);
            }
        }
    }

    public function canRead(User $user, Model $model): bool
    {
        if (! $user->hasValidAssignment()) {
            return false;
        }
        $query = $model->newQueryWithoutScopes()->whereKey($model->getKey());
        $this->constrain($query, $user);

        return $query->exists();
    }

    public function canWrite(User $user, Model $model): bool
    {
        if (! $user->hasValidAssignment()) {
            return false;
        }
        if (! $model instanceof Institution && ! $model instanceof Department
            && ! $model instanceof Category && ! $model instanceof Item) {
            return false;
        }
        if ($user->role === 'superadmin') {
            return true;
        }
        if ($model instanceof Institution || $user->role === 'peminjam') {
            return false;
        }
        if ((int) $model->getAttribute('institution_id') !== $user->institution_id) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }

        return $model instanceof Item
            && (int) $model->getAttribute('department_id') === $user->department_id;
    }
}
