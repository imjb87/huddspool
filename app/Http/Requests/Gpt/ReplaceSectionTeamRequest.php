<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReplaceSectionTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'replacement_team_id' => ['required', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'expected_current_team_id' => ['required', 'integer'],
            'expected_updated_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
