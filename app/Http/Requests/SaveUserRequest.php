<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        return $target instanceof User
            ? ($this->user()?->can('update', $target) ?? false)
            : ($this->user()?->can('create', User::class) ?? false);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $actor = $this->user();
        $target = $this->route('user');
        $superadmin = $actor instanceof User && $actor->role === 'superadmin';
        $role = $this->input('role');
        $institution = $this->input('institution_id');
        $needsDepartment = in_array($role, ['operator'], true);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($target instanceof User ? $target : null)],
            'role' => ['required', Rule::in($superadmin ? ['superadmin', 'admin', 'operator'] : ['operator'])],
            'institution_id' => [
                Rule::requiredIf($role !== 'superadmin'), 'nullable', 'integer',
                Rule::prohibitedIf($role === 'superadmin'),
                Rule::exists('institutions', 'id')->where('is_active', true),
                ...($superadmin ? [] : [Rule::in([$actor instanceof User ? $actor->institution_id : null])]),
            ],
            'department_id' => [
                Rule::requiredIf($needsDepartment), 'nullable', 'integer',
                Rule::prohibitedIf(! $needsDepartment),
                Rule::exists('departments', 'id')->where('institution_id', $institution)->where('is_active', true),
            ],
            'password' => [$target instanceof User ? 'nullable' : 'required', 'string', 'max:255', Password::min(8), 'confirmed'],
        ];
    }
}
