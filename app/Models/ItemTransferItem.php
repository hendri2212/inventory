<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTransferItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'institution_id' => 'integer',
            'item_transfer_id' => 'integer',
            'item_unit_id' => 'integer',
        ];
    }

    /** @return BelongsTo<ItemTransfer, $this> */
    public function itemTransfer(): BelongsTo
    {
        return $this->belongsTo(ItemTransfer::class);
    }

    /** @return BelongsTo<ItemUnit, $this> */
    public function itemUnit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
