<?php

namespace App\Http\Requests;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('item');

        return $item instanceof Item ? ($this->user()?->can('update', $item) ?? false)
            : ($this->user()?->can('create', Item::class) ?? false);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $actor = $this->user();
        $item = $this->route('item');
        $institution = $this->input('institution_id');

        return [
            'institution_id' => ['required', 'integer', Rule::exists('institutions', 'id'),
                ...($actor instanceof User && $actor->role !== 'superadmin' ? [Rule::in([$actor->institution_id])] : []),
                ...($item instanceof Item ? [Rule::in([$item->institution_id])] : [])],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('institution_id', $institution)],
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->where('institution_id', $institution)->ignore($item instanceof Item ? $item : null)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'unit' => ['required', 'string', 'max:30'],
        ];
    }
}
