@php
    $submitLabel = $submitLabel ?? 'Save FAQ';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="question" class="text-sm font-medium text-slate-900 dark:text-stone-200">Question</label>
            <input id="question" name="question" type="text" value="{{ old('question', $faq->question) }}" class="cms-input mt-2" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $faq->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="answer" class="text-sm font-medium text-slate-900 dark:text-stone-200">Answer</label>
            <textarea id="answer" name="answer" rows="10" class="cms-textarea mt-2" required>{{ old('answer', $faq->answer) }}</textarea>
        </div>
    </section>

    <aside class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="category_id" class="text-sm font-medium text-slate-900 dark:text-stone-200">Category</label>
            <select id="category_id" name="category_id" class="cms-select mt-2">
                <option value="">Unassigned</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $faq->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="audience" class="text-sm font-medium text-slate-900 dark:text-stone-200">Audience</label>
            <select id="audience" name="audience" class="cms-select mt-2">
                @foreach ($audienceOptions as $option)
                    <option value="{{ $option }}" @selected(old('audience', $faq->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-slate-900 dark:text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="cms-select mt-2">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $faq->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sort_order" class="text-sm font-medium text-slate-900 dark:text-stone-200">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" class="cms-input mt-2">
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
            <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('is_published', $faq->is_published ?? true))>
            Published FAQ
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
    </aside>
</div>