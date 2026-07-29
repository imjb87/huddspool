<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CorrectResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'expected_draft_version' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'frames' => ['required', 'array', 'size:10'],
            'frames.*' => ['required', 'array:home_player_id,away_player_id,home_score,away_score'],
            'frames.*.home_player_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'frames.*.away_player_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'frames.*.home_score' => ['required', 'integer', 'between:0,1'],
            'frames.*.away_score' => ['required', 'integer', 'between:0,1'],
        ];
    }
}
