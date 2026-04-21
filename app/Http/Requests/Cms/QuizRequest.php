<?php

namespace App\Http\Requests\Cms;

use App\Models\Content;
use App\Models\Quiz;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $quizId = $this->route('quiz')?->id;

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('quizzes', 'slug')->ignore($quizId)],
            'summary' => ['nullable', 'string'],
            'intro_text' => ['nullable', 'string'],
            'audience' => ['required', Rule::in(Content::AUDIENCE_OPTIONS)],
            'visibility' => ['required', Rule::in(Content::VISIBILITY_OPTIONS)],
            'status' => ['required', Rule::in(Content::STATUS_OPTIONS)],
            'published_at' => ['nullable', 'date'],
            'questions' => ['nullable', 'array'],
            'questions.*.prompt' => ['nullable', 'string'],
            'questions.*.help_text' => ['nullable', 'string'],
            'questions.*.question_type' => ['nullable', Rule::in(Quiz::QUESTION_TYPE_OPTIONS)],
            'questions.*.is_active' => ['nullable', 'boolean'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*.option_text' => ['nullable', 'string', 'max:255'],
            'questions.*.options.*.feedback' => ['nullable', 'string'],
            'questions.*.options.*.is_correct' => ['nullable', 'boolean'],
        ];
    }
}