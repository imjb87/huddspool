<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'venue_id' => ['required', 'integer', Rule::exists('venues', 'id')->whereNull('deleted_at')],
            'expected_current_venue_id' => ['present', 'nullable', 'integer'],
        ];
    }
}
