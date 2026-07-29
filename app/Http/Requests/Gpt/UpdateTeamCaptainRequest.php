<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamCaptainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'captain_id' => ['present', 'nullable', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'expected_current_captain_id' => ['present', 'nullable', 'integer'],
        ];
    }
}
