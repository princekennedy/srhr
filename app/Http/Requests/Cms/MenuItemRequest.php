<?php

namespace App\Http\Requests\Cms;

use App\Enums\MenuItemLayoutType;
use App\Support\CurrentWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemRequest extends FormRequest
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
        $itemId = $this->route('item')?->id;
        $websiteId = app(CurrentWebsite::class)->id();

        return [
            'parent_id' => ['nullable', Rule::exists('menus', 'id')->where(fn ($query) => $query->where('website_id', $websiteId)->whereNotNull('parent_id'))],
            'title' => ['required', 'string', 'max:120'],
            'layout_type' => ['required', Rule::in(MenuItemLayoutType::values())],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('menus', 'slug')->where(fn ($query) => $query->where('website_id', $websiteId))->ignore($itemId)],
            'target_reference' => ['nullable', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'visibility' => ['required', Rule::in(['public', 'private', 'restricted'])],
            'open_in_webview' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}