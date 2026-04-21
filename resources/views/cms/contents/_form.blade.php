@php
    $submitLabel = $submitLabel ?? 'Save content';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.35fr_0.8fr]">
    <section class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="title" class="text-sm font-medium text-stone-200">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $content->title) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $content->slug) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="summary" class="text-sm font-medium text-stone-200">Summary</label>
            <textarea id="summary" name="summary" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">{{ old('summary', $content->summary) }}</textarea>
        </div>

        <div>
            <label for="body" class="text-sm font-medium text-stone-200">Body</label>
            <textarea id="body" name="body" rows="14" data-ckeditor-field="content-body" class="mt-2 w-full rounded-3xl border border-white/10 bg-stone-950/60 px-4 py-4 text-white focus:border-emerald-400 focus:outline-none">{{ old('body', $content->body) }}</textarea>
            <p class="mt-2 text-xs text-stone-500">Use the rich text editor to format headings, lists, links, and emphasis for the website and mobile app.</p>
        </div>
    </section>

    <aside class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="content_type" class="text-sm font-medium text-stone-200">Content type</label>
            <select id="content_type" name="content_type" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($typeOptions as $option)
                    <option value="{{ $option }}" @selected(old('content_type', $content->content_type ?: 'page') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="category_id" class="text-sm font-medium text-stone-200">Category</label>
            <select id="category_id" name="category_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                <option value="">Unassigned</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $content->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="text-sm font-medium text-stone-200">Status</label>
            <select id="status" name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($statusOptions as $option)
                    <option value="{{ $option }}" @selected(old('status', $content->status ?: 'draft') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="audience" class="text-sm font-medium text-stone-200">Audience</label>
            <select id="audience" name="audience" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($audienceOptions as $option)
                    <option value="{{ $option }}" @selected(old('audience', $content->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $content->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="featured_image_path" class="text-sm font-medium text-stone-200">Featured image path</label>
            <input id="featured_image_path" name="featured_image_path" type="text" value="{{ old('featured_image_path', $content->featured_image_path) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="storage/content/example.png">
        </div>

        <div>
            <label for="published_at" class="text-sm font-medium text-stone-200">Publish at</label>
            <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $content->published_at?->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">{{ $submitLabel }}</button>
    </aside>
</div>

@once
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
@endonce

<script>
    (() => {
        const field = document.querySelector('[data-ckeditor-field="content-body"]');

        if (!field || field.dataset.ckeditorReady === 'true' || typeof ClassicEditor === 'undefined') {
            return;
        }

        ClassicEditor
            .create(field, {
                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    '|',
                    'undo',
                    'redo',
                ],
            })
            .then((editor) => {
                field.dataset.ckeditorReady = 'true';
                window.srhrContentEditor = editor;
            })
            .catch((error) => {
                console.error('Failed to initialize CKEditor.', error);
            });
    })();
</script>