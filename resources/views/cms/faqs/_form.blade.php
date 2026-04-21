@php
    $submitLabel = $submitLabel ?? 'Save FAQ';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="question" class="text-sm font-medium text-stone-200">Question</label>
            <input id="question" name="question" type="text" value="{{ old('question', $faq->question) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $faq->slug) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="answer" class="text-sm font-medium text-stone-200">Answer</label>
            <textarea id="answer" name="answer" rows="10" class="mt-2 w-full rounded-3xl border border-white/10 bg-stone-950/60 px-4 py-4 text-white focus:border-emerald-400 focus:outline-none" required>{{ old('answer', $faq->answer) }}</textarea>
        </div>
    </section>

    <aside class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="category_id" class="text-sm font-medium text-stone-200">Category</label>
            <select id="category_id" name="category_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                <option value="">Unassigned</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $faq->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="audience" class="text-sm font-medium text-stone-200">Audience</label>
            <select id="audience" name="audience" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($audienceOptions as $option)
                    <option value="{{ $option }}" @selected(old('audience', $faq->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $faq->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sort_order" class="text-sm font-medium text-stone-200">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('is_published', $faq->is_published ?? true))>
            Published FAQ
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">{{ $submitLabel }}</button>
    </aside>
</div>