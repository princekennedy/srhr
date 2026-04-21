@php
    $submitLabel = $submitLabel ?? 'Save service';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="name" class="text-sm font-medium text-slate-900 dark:text-stone-200">Service name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $service->name) }}" class="cms-input mt-2" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $service->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="district" class="text-sm font-medium text-slate-900 dark:text-stone-200">District</label>
                <input id="district" name="district" type="text" value="{{ old('district', $service->district) }}" class="cms-input mt-2">
            </div>

            <div>
                <label for="service_hours" class="text-sm font-medium text-slate-900 dark:text-stone-200">Service hours</label>
                <input id="service_hours" name="service_hours" type="text" value="{{ old('service_hours', $service->service_hours) }}" class="cms-input mt-2">
            </div>
        </div>

        <div>
            <label for="physical_address" class="text-sm font-medium text-slate-900 dark:text-stone-200">Physical address</label>
            <textarea id="physical_address" name="physical_address" rows="3" class="cms-textarea mt-2">{{ old('physical_address', $service->physical_address) }}</textarea>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="contact_phone" class="text-sm font-medium text-slate-900 dark:text-stone-200">Contact phone</label>
                <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $service->contact_phone) }}" class="cms-input mt-2">
            </div>

            <div>
                <label for="contact_email" class="text-sm font-medium text-slate-900 dark:text-stone-200">Contact email</label>
                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $service->contact_email) }}" class="cms-input mt-2">
            </div>
        </div>

        <div>
            <label for="summary" class="text-sm font-medium text-slate-900 dark:text-stone-200">Summary</label>
            <textarea id="summary" name="summary" rows="4" class="cms-textarea mt-2">{{ old('summary', $service->summary) }}</textarea>
        </div>

        <div>
            <label for="services" class="text-sm font-medium text-slate-900 dark:text-stone-200">Service offering details</label>
            <textarea id="services" name="services" rows="6" class="cms-textarea mt-2">{{ old('services', $service->services) }}</textarea>
        </div>
    </section>

    <aside class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="category_id" class="text-sm font-medium text-slate-900 dark:text-stone-200">Category</label>
            <select id="category_id" name="category_id" class="cms-select mt-2">
                <option value="">Unassigned</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $service->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="audience" class="text-sm font-medium text-slate-900 dark:text-stone-200">Audience</label>
            <select id="audience" name="audience" class="cms-select mt-2">
                @foreach ($audienceOptions as $option)
                    <option value="{{ $option }}" @selected(old('audience', $service->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-slate-900 dark:text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="cms-select mt-2">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $service->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
            <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('is_featured', $service->is_featured ?? false))>
            Featured service
        </label>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('is_active', $service->is_active ?? true))>
            Active listing
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
    </aside>
</div>