<?php

namespace App\Http\Requests\Cms;

use App\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $contentId = $this->route('content')?->id;

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('contents', 'slug')->ignore($contentId)],
            'summary' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'content_type' => ['required', Rule::in(Content::TYPE_OPTIONS)],
            'category_id' => ['nullable', 'exists:content_categories,id'],
            'status' => ['required', Rule::in(Content::STATUS_OPTIONS)],
            'audience' => ['required', Rule::in(Content::AUDIENCE_OPTIONS)],
            'visibility' => ['required', Rule::in(Content::VISIBILITY_OPTIONS)],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}