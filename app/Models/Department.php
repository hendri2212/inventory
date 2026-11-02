<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use BelongsToTenant;

    protected $fillable = ['code', 'name', 'description', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'institution_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return HasMany<Item, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        $relation = $this->hasMany(User::class);
        $actor = Auth::user();
        if ($actor instanceof User) {
            $relation->getQuery()->visibleTo($actor);
        } else {
            $relation->whereRaw('1 = 0');
        }

        return $relation;
    }
}
