<?php

namespace App\Http\Requests\Gpt;

use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'expected_updated_at' => ['required', 'date'],
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($this->route('player'))],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('player'))->whereNull('deleted_at')],
            'telephone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'site_role' => ['sometimes', Rule::enum(RoleName::class)],
        ];
    }

    public function messages(): array
    {
        return ['name.unique' => 'A player with this name already exists. Update the existing account instead of creating a duplicate.'];
    }
}
