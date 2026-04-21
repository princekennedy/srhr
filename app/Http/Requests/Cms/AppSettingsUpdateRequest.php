<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;

class AppSettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable'],
            'setting_uploads' => ['nullable', 'array'],
            'setting_uploads.*' => ['nullable', 'file', 'max:5120'],
        ];
    }
}