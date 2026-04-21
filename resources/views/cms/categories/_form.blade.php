@php
    $submitLabel = $submitLabel ?? 'Save category';
@endphp

<div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
    <section class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="name" class="text-sm font-medium text-stone-200">Category name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="description" class="text-sm font-medium text-stone-200">Description</label>
            <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">{{ old('description', $category->description) }}</textarea>
        </div>
    </section>

    <aside class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="sort_order" class="text-sm font-medium text-stone-200">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('is_active', $category->is_active ?? true))>
            Active category
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">{{ $submitLabel }}</button>
    </aside>
</div>