<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Support\InventoryAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $query): void {
            $user = Auth::user();
            if (! $user instanceof User || ! $user->hasValidAssignment()) {
                $query->whereRaw('1 = 0');

                return;
            }
            app(InventoryAccess::class)->constrain($query, $user);
        });

        static::saving(function ($model): void {
            $user = Auth::user();
            if (! $user instanceof User || ! app(InventoryAccess::class)->canWrite($user, $model)) {
                throw new AuthorizationException;
            }
            if ($model->exists && ! $model->newQuery()->whereKey($model->getKey())->exists()) {
                throw new AuthorizationException;
            }
        });
        static::deleting(function ($model): void {
            $user = Auth::user();
            if (! $user instanceof User || ! app(InventoryAccess::class)->canWrite($user, $model)
                || ! $model->newQuery()->whereKey($model->getKey())->exists()) {
                throw new AuthorizationException;
            }
        });
    }
}
