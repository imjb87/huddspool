<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFixtureDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'fixture_date' => ['required', 'date'],
            'expected_current_fixture_date' => ['required', 'date'],
        ];
    }
}
