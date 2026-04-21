<?php

namespace App\Http\Requests\Cms;

use App\Models\Content;
use App\Support\CurrentWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $faqId = $this->route('faq')?->id;
        $websiteId = app(CurrentWebsite::class)->id();

        return [
            'question' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('faqs', 'slug')->where(fn ($query) => $query->where('website_id', $websiteId))->ignore($faqId)],
            'answer' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:content_categories,id'],
            'audience' => ['required', Rule::in(Content::AUDIENCE_OPTIONS)],
            'visibility' => ['required', Rule::in(Content::VISIBILITY_OPTIONS)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}