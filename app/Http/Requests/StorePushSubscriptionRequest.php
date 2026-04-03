<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePushSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'url', 'max:2048'],
            'public_key' => ['required', 'string', 'max:4096'],
            'auth_token' => ['required', 'string', 'max:4096'],
            'content_encoding' => ['nullable', 'string', 'max:255'],
        ];
    }
}
