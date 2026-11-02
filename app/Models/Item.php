<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use BelongsToTenant;

    protected $fillable = ['code', 'name', 'description', 'quantity', 'unit', 'purchase_year', 'purchase_price', 'photo_path', 'condition', 'condition_notes', 'disposed_at', 'disposal_notes', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'institution_id' => 'integer',
            'is_active' => 'boolean',
            'department_id' => 'integer',
            'category_id' => 'integer',
            'quantity' => 'integer',
            'purchase_year' => 'integer',
            'purchase_price' => 'decimal:2',
            'disposed_at' => 'datetime',
        ];
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

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
