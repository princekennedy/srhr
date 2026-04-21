@php
    $submitLabel = $submitLabel ?? 'Save quiz';
    $questions = old('questions', $questionBlueprints);
@endphp

<div class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="cms-card cms-gradient-card space-y-5 p-6">
            <div>
                <label for="title" class="text-sm font-medium text-slate-900 dark:text-stone-200">Quiz title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $quiz->title) }}" class="cms-input mt-2" required>
            </div>

            <div>
                <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $quiz->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
            </div>

            <div>
                <label for="summary" class="text-sm font-medium text-slate-900 dark:text-stone-200">Summary</label>
                <textarea id="summary" name="summary" rows="4" class="cms-textarea mt-2">{{ old('summary', $quiz->summary) }}</textarea>
            </div>

            <div>
                <label for="intro_text" class="text-sm font-medium text-slate-900 dark:text-stone-200">Intro text</label>
                <textarea id="intro_text" name="intro_text" rows="6" class="cms-textarea mt-2">{{ old('intro_text', $quiz->intro_text) }}</textarea>
            </div>
        </section>

        <aside class="cms-card cms-gradient-card space-y-5 p-6">
            <div>
                <label for="audience" class="text-sm font-medium text-slate-900 dark:text-stone-200">Audience</label>
                <select id="audience" name="audience" class="cms-select mt-2">
                    @foreach ($audienceOptions as $option)
                        <option value="{{ $option }}" @selected(old('audience', $quiz->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="visibility" class="text-sm font-medium text-slate-900 dark:text-stone-200">Visibility</label>
                <select id="visibility" name="visibility" class="cms-select mt-2">
                    @foreach ($visibilityOptions as $option)
                        <option value="{{ $option }}" @selected(old('visibility', $quiz->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="text-sm font-medium text-slate-900 dark:text-stone-200">Status</label>
                <select id="status" name="status" class="cms-select mt-2">
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option }}" @selected(old('status', $quiz->status ?: 'draft') === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="published_at" class="text-sm font-medium text-slate-900 dark:text-stone-200">Publish at</label>
                <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $quiz->published_at?->format('Y-m-d\TH:i')) }}" class="cms-input mt-2">
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
        </aside>
    </div>

    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="cms-heading text-lg font-semibold">Questions and options</h3>
                <p class="text-sm text-slate-500 dark:text-stone-400">Each quiz ships with up to three single-choice questions and four options per question in this CMS version.</p>
            </div>
        </div>

        @foreach ($questions as $questionIndex => $question)
            <article class="cms-card cms-gradient-card p-6">
                <div class="grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="space-y-4">
                        <div>
                            <label for="questions_{{ $questionIndex }}_prompt" class="text-sm font-medium text-slate-900 dark:text-stone-200">Question {{ $questionIndex + 1 }}</label>
                            <input id="questions_{{ $questionIndex }}_prompt" name="questions[{{ $questionIndex }}][prompt]" type="text" value="{{ $question['prompt'] ?? null }}" class="cms-input mt-2">
                        </div>

                        <div>
                            <label for="questions_{{ $questionIndex }}_help_text" class="text-sm font-medium text-slate-900 dark:text-stone-200">Help text</label>
                            <textarea id="questions_{{ $questionIndex }}_help_text" name="questions[{{ $questionIndex }}][help_text]" rows="3" class="cms-textarea mt-2">{{ $question['help_text'] ?? null }}</textarea>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="questions_{{ $questionIndex }}_question_type" class="text-sm font-medium text-slate-900 dark:text-stone-200">Question type</label>
                            <select id="questions_{{ $questionIndex }}_question_type" name="questions[{{ $questionIndex }}][question_type]" class="cms-select mt-2">
                                <option value="single_choice" @selected(($question['question_type'] ?? 'single_choice') === 'single_choice')>Single choice</option>
                            </select>
                        </div>

                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
                            <input type="checkbox" name="questions[{{ $questionIndex }}][is_active]" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked($question['is_active'] ?? true)>
                            Active question
                        </label>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @foreach (($question['options'] ?? []) as $optionIndex => $option)
                        <div class="cms-card bg-white/65 p-4 dark:bg-slate-950/30">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Option {{ $optionIndex + 1 }}</p>
                            <div class="mt-3 space-y-3">
                                <input name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]" type="text" value="{{ $option['option_text'] ?? null }}" class="cms-input" placeholder="Option text">
                                <textarea name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][feedback]" rows="3" class="cms-textarea" placeholder="Feedback shown after answer">{{ $option['feedback'] ?? null }}</textarea>
                                <label class="flex items-center gap-3 text-sm text-slate-700 dark:text-stone-200">
                                    <input type="checkbox" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][is_correct]" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked($option['is_correct'] ?? false)>
                                    Correct answer
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
</div>