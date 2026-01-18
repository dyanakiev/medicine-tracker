<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'compact_view' => ['sometimes', 'boolean'],
            'locale' => [
                'sometimes',
                'string',
                Rule::in(array_keys(config('languages.supported', ['en' => 'English']))),
            ],
            'timezone' => [
                'sometimes',
                'string',
                Rule::in(\DateTimeZone::listIdentifiers()),
            ],
        ];
    }
}
