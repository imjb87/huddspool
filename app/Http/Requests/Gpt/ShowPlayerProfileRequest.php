<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;

class ShowPlayerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'frames_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
