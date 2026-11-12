<?php

namespace App\Http\Requests;

use App\Models\ItemUnit;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveItemUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $unit = $this->route('item_unit');

        return $unit instanceof ItemUnit ? ($this->user()?->can('update', $unit) ?? false)
            : ($this->user()?->can('create', ItemUnit::class) ?? false);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $actor = $this->user();
        $unit = $this->route('item_unit');
        $institution = $this->input('institution_id');

        return [
            'institution_id' => ['required', 'integer', Rule::exists('institutions', 'id')->where('is_active', true),
                ...($actor instanceof User && $actor->role !== 'superadmin' ? [Rule::in([$actor->institution_id])] : []),
                ...($unit instanceof ItemUnit ? [Rule::in([$unit->institution_id])] : [])],
            'item_id' => ['required', 'integer', Rule::exists('items', 'id')->where('institution_id', $institution)],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where('institution_id', $institution)->where('is_active', true),
                ...($actor instanceof User && $actor->role === 'operator' ? [Rule::in([$actor->department_id])] : [])],
            'room_id' => ['nullable', 'integer', Rule::exists('rooms', 'id')->where('institution_id', $institution)
                ->where(fn ($query) => $query->where('is_active', true)
                    ->orWhere('id', $unit instanceof ItemUnit ? $unit->getAttribute('room_id') : null))],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'purchase_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99', 'decimal:0,2'],
            'funding_source' => ['nullable', Rule::in(['BOSP', 'BOSDA', 'Bantuan'])],
            'condition' => ['nullable', Rule::in(['good', 'minor_damage', 'major_damage'])],
            'condition_notes' => ['nullable', 'string', 'max:5000'],
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo_ids' => ['sometimes', 'array'],
            'remove_photo_ids.*' => ['required', 'integer', 'distinct',
                Rule::exists('item_unit_photos', 'id')->where('item_unit_id', $unit instanceof ItemUnit ? $unit->getKey() : null)],
        ];
    }
}
