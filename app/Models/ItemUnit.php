<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemUnit extends Model
{
    use BelongsToTenant;

    protected $fillable = ['code', 'serial_number', 'purchase_year', 'purchase_price', 'funding_source', 'photo_path', 'condition', 'condition_notes', 'disposed_at', 'disposal_notes', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'institution_id' => 'integer',
            'item_id' => 'integer',
            'department_id' => 'integer',
            'room_id' => 'integer',
            'is_active' => 'boolean',
            'purchase_year' => 'integer',
            'purchase_price' => 'decimal:2',
            'disposed_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return HasMany<LoanItem, $this> */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }
}
