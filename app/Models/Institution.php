<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Institution extends Model
{
    use BelongsToTenant;

    protected $fillable = ['code', 'name', 'address', 'phone', 'email', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<Department, $this> */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /** @return HasMany<Category, $this> */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /** @return HasMany<Item, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /** @return HasMany<Room, $this> */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
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
