<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\QuizRequest;
use App\Models\Content;
use App\Models\Quiz;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class QuizController extends Controller
{
    public function index(): View
    {
        return view('cms.quizzes.index', [
            'quizzes' => Quiz::query()
                ->withCount('questions')
                ->latest('updated_at')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('cms.quizzes.create', [
            'quiz' => new Quiz(),
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
            'statusOptions' => Content::STATUS_OPTIONS,
            'questionBlueprints' => $this->defaultQuestionBlueprints(),
        ]);
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        $quiz = Quiz::create($this->validatedPayload($request));
        $this->syncQuestions($quiz, $request->validated('questions', []));

        return redirect()
            ->route('cms.quizzes.index')
            ->with('status', 'Quiz created.');
    }

    public function edit(Quiz $quiz): View
    {
        $quiz->load('questions.options');

        return view('cms.quizzes.edit', [
            'quiz' => $quiz,
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
            'statusOptions' => Content::STATUS_OPTIONS,
            'questionBlueprints' => $quiz->questions->map(function ($question): array {
                return [
                    'prompt' => $question->prompt,
                    'help_text' => $question->help_text,
                    'question_type' => $question->question_type,
                    'is_active' => $question->is_active,
                    'options' => $question->options->map(function ($option): array {
                        return [
                            'option_text' => $option->option_text,
                            'feedback' => $option->feedback,
                            'is_correct' => $option->is_correct,
                        ];
                    })->all(),
                ];
            })->values()->all() ?: $this->defaultQuestionBlueprints(),
        ]);
    }

    public function update(QuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($this->validatedPayload($request, $quiz));
        $this->syncQuestions($quiz, $request->validated('questions', []));

        return redirect()
            ->route('cms.quizzes.index')
            ->with('status', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()
            ->route('cms.quizzes.index')
            ->with('status', 'Quiz deleted.');
    }

    private function validatedPayload(QuizRequest $request, ?Quiz $quiz = null): array
    {
        $payload = $request->safe()->except(['questions']);
        $userId = $request->user()?->id;

        if (($payload['status'] ?? null) === 'published' && blank($payload['published_at'] ?? null)) {
            $payload['published_at'] = Carbon::now();
        }

        $payload['created_by'] = $quiz?->created_by ?? $userId;
        $payload['updated_by'] = $userId;

        return $payload;
    }

    private function syncQuestions(Quiz $quiz, array $questions): void
    {
        $quiz->questions()->delete();

        foreach (array_values($questions) as $questionIndex => $questionData) {
            $prompt = trim((string) ($questionData['prompt'] ?? ''));
            $options = $questionData['options'] ?? [];
            $hasAnyOption = collect($options)->contains(fn (array $option): bool => filled(trim((string) ($option['option_text'] ?? ''))));

            if ($prompt === '' && ! $hasAnyOption) {
                continue;
            }

            $question = $quiz->questions()->create([
                'prompt' => $prompt,
                'help_text' => $questionData['help_text'] ?? null,
                'question_type' => $questionData['question_type'] ?? 'single_choice',
                'sort_order' => $questionIndex + 1,
                'is_active' => (bool) ($questionData['is_active'] ?? true),
            ]);

            foreach (array_values($options) as $optionIndex => $optionData) {
                $optionText = trim((string) ($optionData['option_text'] ?? ''));

                if ($optionText === '') {
                    continue;
                }

                $question->options()->create([
                    'option_text' => $optionText,
                    'feedback' => $optionData['feedback'] ?? null,
                    'is_correct' => (bool) ($optionData['is_correct'] ?? false),
                    'sort_order' => $optionIndex + 1,
                ]);
            }
        }
    }

    private function defaultQuestionBlueprints(): array
    {
        return collect(range(1, 3))->map(fn (): array => [
            'prompt' => null,
            'help_text' => null,
            'question_type' => 'single_choice',
            'is_active' => true,
            'options' => collect(range(1, 4))->map(fn (): array => [
                'option_text' => null,
                'feedback' => null,
                'is_correct' => false,
            ])->all(),
        ])->all();
    }
}