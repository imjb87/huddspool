<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'destination_team_id' => [
                'required',
                'integer',
                Rule::exists('teams', 'id')->whereNull('deleted_at'),
            ],
            'expected_current_team_id' => ['present', 'nullable', 'integer'],
            'make_destination_captain' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'expected_current_team_id.present' => 'The player’s expected current team must be supplied, using null when they have no team.',
        ];
    }
}
