<?php

namespace App\Http\Requests\Cms;

use App\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')?->id;

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('service_centers', 'slug')->ignore($serviceId)],
            'category_id' => ['nullable', 'exists:content_categories,id'],
            'district' => ['nullable', 'string', 'max:120'],
            'physical_address' => ['nullable', 'string'],
            'contact_phone' => ['nullable', 'string', 'max:80'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'service_hours' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'audience' => ['required', Rule::in(Content::AUDIENCE_OPTIONS)],
            'visibility' => ['required', Rule::in(Content::VISIBILITY_OPTIONS)],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}