<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\ItemTransfer;
use App\Models\ItemTransferItem;
use App\Models\ItemUnit;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Room;
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
        if ($user->role === 'operator') {
            if ($model instanceof Department) {
                $query->where($model->qualifyColumn('id'), $user->department_id);
            } elseif ($model instanceof ItemUnit || $model instanceof Loan) {
                $query->where($model->qualifyColumn('department_id'), $user->department_id);
            } elseif ($model instanceof LoanItem) {
                $query->whereHas('loan', fn ($loan) => $loan->where('department_id', $user->department_id));
            } elseif ($model instanceof ItemTransfer) {
                $query->where(fn ($transfer) => $transfer->where('from_department_id', $user->department_id)
                    ->orWhere('to_department_id', $user->department_id));
            } elseif ($model instanceof ItemTransferItem) {
                $query->whereHas('itemTransfer');
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
            && ! $model instanceof Category && ! $model instanceof Item
            && ! $model instanceof ItemUnit && ! $model instanceof Room
            && ! $model instanceof Loan && ! $model instanceof LoanItem
            && ! $model instanceof ItemTransfer && ! $model instanceof ItemTransferItem) {
            return false;
        }
        if ($user->role === 'superadmin') {
            return true;
        }
        if ($model instanceof Institution) {
            return false;
        }
        if ((int) $model->getAttribute('institution_id') !== $user->institution_id) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }

        if ($model instanceof ItemUnit || $model instanceof Loan) {
            return (int) $model->getAttribute('department_id') === $user->department_id;
        }
        if ($model instanceof LoanItem) {
            return Loan::query()->whereKey($model->getAttribute('loan_id'))->exists()
                && ItemUnit::query()->whereKey($model->getAttribute('item_unit_id'))->exists();
        }

        // Transfers require a dedicated workflow; generic model writes by operators are denied.
        return false;
    }
}
