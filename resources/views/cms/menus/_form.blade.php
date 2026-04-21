@php
    $submitLabel = $submitLabel ?? 'Save menu';
@endphp

<div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
    <section class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="name" class="text-sm font-medium text-stone-200">Menu name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $menu->name) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $menu->slug) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="description" class="text-sm font-medium text-stone-200">Description</label>
            <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">{{ old('description', $menu->description) }}</textarea>
        </div>
    </section>

    <aside class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="location" class="text-sm font-medium text-stone-200">Location key</label>
            <input id="location" name="location" type="text" value="{{ old('location', $menu->location) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="home-primary">
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('is_active', $menu->is_active ?? true))>
            Active menu
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">{{ $submitLabel }}</button>
    </aside>
</div>