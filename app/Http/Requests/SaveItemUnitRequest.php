<?php

namespace App\Http\Requests;

use App\Models\ItemUnit;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveItemUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $unit = $this->route('item_unit');

        return $unit instanceof ItemUnit ? ($this->user()?->can('update', $unit) ?? false)
            : ($this->user()?->can('create', ItemUnit::class) ?? false);
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $unit = $this->route('item_unit');
            if ($unit instanceof ItemUnit && $this->input('room_id') != $unit->getAttribute('room_id')) {
                $validator->errors()->add('room_id', 'Perubahan ruang harus melalui transaksi mutasi.');
            }
        }];
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
            'item_id' => ['required', 'integer', Rule::exists('items', 'id')->where('institution_id', $institution),
                ...($unit instanceof ItemUnit ? [Rule::in([$unit->getAttribute('item_id')])] : [])],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where('institution_id', $institution)->where('is_active', true),
                ...($actor instanceof User && $actor->role === 'operator' ? [Rule::in([$actor->department_id])] : []),
                ...($unit instanceof ItemUnit ? [Rule::in([$unit->getAttribute('department_id')])] : [])],
            'room_id' => ['nullable', 'integer', Rule::exists('rooms', 'id')->where('institution_id', $institution)->where('is_active', true),
                ...($unit instanceof ItemUnit ? [Rule::in([$unit->getAttribute('room_id')])] : [])],
            'code' => ['required', 'string', 'max:50', Rule::unique('item_units', 'code')->where('institution_id', $institution)->ignore($unit instanceof ItemUnit ? $unit : null)],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'purchase_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99', 'decimal:0,2'],
            'funding_source' => ['nullable', Rule::in(['BOSP', 'BOSDA', 'Bantuan'])],
            'condition' => ['nullable', Rule::in(['good', 'minor_damage', 'major_damage'])],
            'condition_notes' => ['nullable', 'string', 'max:5000'],
            'disposed_at' => ['nullable', 'date_format:Y-m-d'],
            'disposal_notes' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo' => ['sometimes', 'boolean'],
        ];
    }
}
