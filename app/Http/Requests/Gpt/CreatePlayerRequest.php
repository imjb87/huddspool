<?php

namespace App\Http\Requests\Gpt;

use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'telephone' => ['nullable', 'string', 'max:255'],
            'team_id' => ['nullable', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'site_role' => ['required', Rule::enum(RoleName::class)],
        ];
    }

    public function messages(): array
    {
        return ['name.unique' => 'A player with this name already exists. Find the existing account instead of creating a duplicate.'];
    }
}
