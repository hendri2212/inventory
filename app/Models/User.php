<?php

namespace App\Models;

use Carbon\CarbonImmutable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property 'superadmin'|'admin'|'operator'|'peminjam' $role
 * @property int|null $institution_id
 * @property int|null $department_id
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            $actor = Auth::user();
            if ($actor instanceof User && (! $user->exists || $user->isDirty(['role', 'institution_id', 'department_id']))) {
                $actor = $actor->fresh();
                if (! $actor instanceof User || ! $actor->hasValidAssignment()) {
                    throw new AuthorizationException;
                }
                if ($actor->role !== 'superadmin') {
                    $original = $user->exists ? User::find($user->id) : null;
                    if ($actor->role !== 'admin' || $user->institution_id !== $actor->institution_id
                        || ! in_array($user->role, ['operator', 'peminjam'], true)
                        || ($original !== null && ($original->institution_id !== $actor->institution_id
                            || ! in_array($original->role, ['operator', 'peminjam'], true)))) {
                        throw new AuthorizationException;
                    }
                }
            }
            if ((! $user->exists || $user->isDirty(['role', 'institution_id', 'department_id'])) && ! $user->hasValidAssignment()) {
                throw ValidationException::withMessages([
                    'department_id' => 'Role, instansi, dan bidang akun tidak sesuai.',
                ]);
            }
        });
    }

    public function hasValidAssignment(): bool
    {
        if ($this->role === 'superadmin') {
            return $this->institution_id === null && $this->department_id === null;
        }

        if (! DB::table('institutions')->where('id', $this->institution_id)->where('is_active', true)->exists()) {
            return false;
        }

        if ($this->role === 'admin') {
            return $this->department_id === null;
        }

        return in_array($this->role, ['operator', 'peminjam'], true)
            && DB::table('departments')->where('id', $this->department_id)
                ->where('institution_id', $this->institution_id)->where('is_active', true)->exists();
    }

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeVisibleTo(Builder $query, User $actor): Builder
    {
        if (! $actor->hasValidAssignment()) {
            return $query->whereRaw('1 = 0');
        }
        if ($actor->role === 'superadmin') {
            return $query;
        }
        $query->where('institution_id', $actor->institution_id)->where('role', '!=', 'superadmin');

        return $actor->role === 'admin' ? $query : $query->where('department_id', $actor->department_id);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'immutable_datetime',
            'institution_id' => 'integer',
            'department_id' => 'integer',
            'password' => 'hashed',
        ];
    }
}
