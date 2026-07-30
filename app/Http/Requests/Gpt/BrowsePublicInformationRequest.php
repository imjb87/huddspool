<?php

namespace App\Http\Requests\Gpt;

use Illuminate\Foundation\Http\FormRequest;

class BrowsePublicInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'path' => ['required', 'string', 'max:500', 'regex:/^\/(?:$|news(?:\/|$)|download-centre$|rulesets(?:\/|$)|fixtures\/\d+(?:\?|$)|results\/\d+(?:\?|$)|players\/\d+(?:\?|$)|teams\/\d+(?:\?|$)|knockouts(?:\/|$)|history(?:\/|$)|venues\/\d+(?:\?|$)|pages(?:\/|$))[^#]*$/'],
        ];
    }

    public function messages(): array
    {
        return ['path.regex' => 'Choose a valid public Huddspool page path.'];
    }
}
