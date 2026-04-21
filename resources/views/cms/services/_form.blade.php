@php
    $submitLabel = $submitLabel ?? 'Save service';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="name" class="text-sm font-medium text-stone-200">Service name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $service->name) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $service->slug) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="auto-generated if left blank">
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="district" class="text-sm font-medium text-stone-200">District</label>
                <input id="district" name="district" type="text" value="{{ old('district', $service->district) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
            </div>

            <div>
                <label for="service_hours" class="text-sm font-medium text-stone-200">Service hours</label>
                <input id="service_hours" name="service_hours" type="text" value="{{ old('service_hours', $service->service_hours) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
            </div>
        </div>

        <div>
            <label for="physical_address" class="text-sm font-medium text-stone-200">Physical address</label>
            <textarea id="physical_address" name="physical_address" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">{{ old('physical_address', $service->physical_address) }}</textarea>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="contact_phone" class="text-sm font-medium text-stone-200">Contact phone</label>
                <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $service->contact_phone) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
            </div>

            <div>
                <label for="contact_email" class="text-sm font-medium text-stone-200">Contact email</label>
                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $service->contact_email) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
            </div>
        </div>

        <div>
            <label for="summary" class="text-sm font-medium text-stone-200">Summary</label>
            <textarea id="summary" name="summary" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">{{ old('summary', $service->summary) }}</textarea>
        </div>

        <div>
            <label for="services" class="text-sm font-medium text-stone-200">Service offering details</label>
            <textarea id="services" name="services" rows="6" class="mt-2 w-full rounded-3xl border border-white/10 bg-stone-950/60 px-4 py-4 text-white focus:border-emerald-400 focus:outline-none">{{ old('services', $service->services) }}</textarea>
        </div>
    </section>

    <aside class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="category_id" class="text-sm font-medium text-stone-200">Category</label>
            <select id="category_id" name="category_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                <option value="">Unassigned</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $service->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="audience" class="text-sm font-medium text-stone-200">Audience</label>
            <select id="audience" name="audience" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($audienceOptions as $option)
                    <option value="{{ $option }}" @selected(old('audience', $service->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $service->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('is_featured', $service->is_featured ?? false))>
            Featured service
        </label>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('is_active', $service->is_active ?? true))>
            Active listing
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">{{ $submitLabel }}</button>
    </aside>
</div>