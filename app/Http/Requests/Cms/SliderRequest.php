<?php

namespace App\Http\Requests\Cms;

use App\Enums\SliderLayoutType;
use App\Support\CurrentWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items');

        if (is_array($items) && $items !== []) {
            return;
        }

        $fallbackTitle = trim((string) $this->input('title', ''));

        if ($fallbackTitle === '') {
            return;
        }

        $this->merge([
            'items' => [[
                'title' => $fallbackTitle,
                'caption' => $this->input('caption'),
                'primary_button_text' => $this->input('primary_button_text'),
                'primary_button_link' => $this->input('primary_button_link'),
                'secondary_button_text' => $this->input('secondary_button_text'),
                'secondary_button_link' => $this->input('secondary_button_link'),
                'sort_order' => $this->input('sort_order', 0),
                'is_active' => $this->boolean('is_active', true),
            ]],
        ]);
    }

    public function rules(): array
    {
        $sliderId = $this->route('slider')?->id;
        $websiteId = app(CurrentWebsite::class)->id();

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('sliders', 'slug')->where(fn ($query) => $query->where('website_id', $websiteId))->ignore($sliderId)],
            'kicker' => ['nullable', 'string', 'max:120'],
            'layout_type' => ['required', Rule::in(SliderLayoutType::values())],
            'caption' => ['nullable', 'string'],
            'primary_button_text' => ['nullable', 'string', 'max:80'],
            'primary_button_link' => ['nullable', 'string', 'max:255'],
            'secondary_button_text' => ['nullable', 'string', 'max:80'],
            'secondary_button_link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image_upload' => ['nullable', 'image', 'max:5120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', Rule::exists('slider_items', 'id')->where(fn ($query) => $query->where('website_id', $websiteId))],
            'items.*.title' => ['required', 'string', 'max:160'],
            'items.*.caption' => ['nullable', 'string'],
            'items.*.primary_button_text' => ['nullable', 'string', 'max:80'],
            'items.*.primary_button_link' => ['nullable', 'string', 'max:255'],
            'items.*.secondary_button_text' => ['nullable', 'string', 'max:80'],
            'items.*.secondary_button_link' => ['nullable', 'string', 'max:255'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'items.*.is_active' => ['nullable', 'boolean'],
            'items.*.image_upload' => ['nullable', 'image', 'max:5120'],
        ];
    }
}