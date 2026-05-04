@php
    $submitLabel = $submitLabel ?? 'Save slide';
    $existingItems = $slider->relationLoaded('items') ? $slider->items : collect();
    $existingItemImageUrls = $existingItems
        ->mapWithKeys(fn ($item) => [$item->id => $item->imageUrl()])
        ->all();

    $sliderItems = old('items');

    if (is_array($sliderItems)) {
        $sliderItems = collect($sliderItems);
    } else {
        $sliderItems = $existingItems->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'caption' => $item->caption,
            'primary_button_text' => $item->primary_button_text,
            'primary_button_link' => $item->primary_button_link,
            'secondary_button_text' => $item->secondary_button_text,
            'secondary_button_link' => $item->secondary_button_link,
            'sort_order' => $item->sort_order,
            'is_active' => $item->is_active,
            'existing_image_url' => $item->imageUrl(),
        ]);

        if ($sliderItems->isEmpty()) {
            $sliderItems = collect([[
                'title' => $slider->title,
                'caption' => $slider->caption,
                'primary_button_text' => $slider->primary_button_text,
                'primary_button_link' => $slider->primary_button_link,
                'secondary_button_text' => $slider->secondary_button_text,
                'secondary_button_link' => $slider->secondary_button_link,
                'sort_order' => 0,
                'is_active' => true,
                'existing_image_url' => $slider->imageUrl(),
            ]]);
        }
    }

    $nextItemIndex = ($sliderItems->keys()->map(fn ($key) => (int) $key)->max() ?? -1) + 1;
@endphp

<div class="grid gap-6 xl:grid-cols-[1.35fr_0.8fr]">
    <section class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="title" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slider name</label>
            <input id="title" name="title" type="text" value="{{ old('title', $slider->title) }}" class="cms-input mt-2" required>
            <p class="mt-2 text-xs text-slate-500 dark:text-stone-400">This names the slider group. Individual slide content lives in the slider items below.</p>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $slider->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="kicker" class="text-sm font-medium text-slate-900 dark:text-stone-200">Kicker</label>
            <input id="kicker" name="kicker" type="text" value="{{ old('kicker', $slider->kicker) }}" class="cms-input mt-2" placeholder="Simple. Elegant. Effective.">
        </div>

        <div>
            <label for="layout_type" class="text-sm font-medium text-slate-900 dark:text-stone-200">Layout</label>
            <x-cms.layout-picker
                name="layout_type"
                section="sliders"
                :options="$layoutOptions"
                :value="old('layout_type', $slider->normalizedLayoutType())"
                label="Slider"
            />
        </div>

        <section class="rounded-[1.75rem] border border-slate-200/80 bg-white/70 p-5 shadow-sm shadow-slate-200/40 dark:border-white/10 dark:bg-slate-950/30" data-slider-item-manager>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Slider items</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-stone-400">Each item becomes one slide with its own image, title, caption, and call-to-action buttons.</p>
                </div>
                <button type="button" class="inline-flex items-center justify-center rounded-full border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-700 transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100 dark:border-sky-400/30 dark:bg-sky-500/10 dark:text-sky-300" data-slider-item-add>
                    Add slide item
                </button>
            </div>

            <div class="mt-5 space-y-5" data-slider-item-list data-next-index="{{ $nextItemIndex }}">
                @foreach ($sliderItems as $itemIndex => $item)
                    @php
                        $existingImageUrl = data_get($item, 'existing_image_url') ?: ($existingItemImageUrls[data_get($item, 'id')] ?? null);
                    @endphp
                    <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/40 dark:border-white/10 dark:bg-slate-950/40" data-slider-item-card>
                        <div class="flex flex-col gap-3 border-b border-slate-200/80 pb-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-stone-400" data-slider-item-label>Slide item {{ $loop->iteration }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-stone-400">Add the image and copy for this slide.</p>
                            </div>
                            <button type="button" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:border-rose-300 hover:bg-rose-100 dark:border-rose-400/30 dark:bg-rose-500/10 dark:text-rose-300" data-slider-item-remove>
                                Remove item
                            </button>
                        </div>

                        <input type="hidden" name="items[{{ $itemIndex }}][id]" value="{{ data_get($item, 'id') }}">

                        <div class="mt-5 grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
                            <div class="space-y-5">
                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Item title</label>
                                    <input name="items[{{ $itemIndex }}][title]" type="text" value="{{ data_get($item, 'title') }}" class="cms-input mt-2" required>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Caption</label>
                                    <textarea name="items[{{ $itemIndex }}][caption]" rows="4" class="cms-textarea mt-2">{{ data_get($item, 'caption') }}</textarea>
                                </div>

                                <div class="grid gap-5 md:grid-cols-2">
                                    <div>
                                        <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button text</label>
                                        <input name="items[{{ $itemIndex }}][primary_button_text]" type="text" value="{{ data_get($item, 'primary_button_text') }}" class="cms-input mt-2">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button link</label>
                                        <input name="items[{{ $itemIndex }}][primary_button_link]" type="text" value="{{ data_get($item, 'primary_button_link') }}" class="cms-input mt-2" placeholder="#features">
                                    </div>
                                </div>

                                <div class="grid gap-5 md:grid-cols-2">
                                    <div>
                                        <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button text</label>
                                        <input name="items[{{ $itemIndex }}][secondary_button_text]" type="text" value="{{ data_get($item, 'secondary_button_text') }}" class="cms-input mt-2">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button link</label>
                                        <input name="items[{{ $itemIndex }}][secondary_button_link]" type="text" value="{{ data_get($item, 'secondary_button_link') }}" class="cms-input mt-2" placeholder="#contact">
                                    </div>
                                </div>
                            </div>

                            <aside class="space-y-5 rounded-[1.25rem] border border-slate-200/80 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-slate-950/50">
                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Slide image</label>
                                    <input name="items[{{ $itemIndex }}][image_upload]" type="file" accept="image/*" class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300">
                                    @if ($existingImageUrl)
                                        <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200/70 bg-white/70 p-2 dark:border-white/10 dark:bg-slate-950/30">
                                            <img src="{{ $existingImageUrl }}" alt="{{ data_get($item, 'title', 'Slider item image') }}" class="h-40 w-full rounded-xl object-cover">
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Item sort order</label>
                                    <input name="items[{{ $itemIndex }}][sort_order]" type="number" min="0" value="{{ data_get($item, 'sort_order', $loop->index) }}" class="cms-input mt-2">
                                </div>

                                <label class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
                                    <input type="checkbox" name="items[{{ $itemIndex }}][is_active]" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(data_get($item, 'is_active', true))>
                                    Active slide item
                                </label>
                            </aside>
                        </div>
                    </article>
                @endforeach
            </div>

            <template data-slider-item-template>
                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/40 dark:border-white/10 dark:bg-slate-950/40" data-slider-item-card>
                    <div class="flex flex-col gap-3 border-b border-slate-200/80 pb-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-stone-400" data-slider-item-label>Slide item</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-stone-400">Add the image and copy for this slide.</p>
                        </div>
                        <button type="button" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:border-rose-300 hover:bg-rose-100 dark:border-rose-400/30 dark:bg-rose-500/10 dark:text-rose-300" data-slider-item-remove>
                            Remove item
                        </button>
                    </div>

                    <input type="hidden" name="items[__INDEX__][id]" value="">

                    <div class="mt-5 grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
                        <div class="space-y-5">
                            <div>
                                <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Item title</label>
                                <input name="items[__INDEX__][title]" type="text" value="" class="cms-input mt-2" required>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Caption</label>
                                <textarea name="items[__INDEX__][caption]" rows="4" class="cms-textarea mt-2"></textarea>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button text</label>
                                    <input name="items[__INDEX__][primary_button_text]" type="text" value="" class="cms-input mt-2">
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button link</label>
                                    <input name="items[__INDEX__][primary_button_link]" type="text" value="" class="cms-input mt-2" placeholder="#features">
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button text</label>
                                    <input name="items[__INDEX__][secondary_button_text]" type="text" value="" class="cms-input mt-2">
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button link</label>
                                    <input name="items[__INDEX__][secondary_button_link]" type="text" value="" class="cms-input mt-2" placeholder="#contact">
                                </div>
                            </div>
                        </div>

                        <aside class="space-y-5 rounded-[1.25rem] border border-slate-200/80 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-slate-950/50">
                            <div>
                                <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Slide image</label>
                                <input name="items[__INDEX__][image_upload]" type="file" accept="image/*" class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300">
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-900 dark:text-stone-200">Item sort order</label>
                                <input name="items[__INDEX__][sort_order]" type="number" min="0" value="0" class="cms-input mt-2">
                            </div>

                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
                                <input type="checkbox" name="items[__INDEX__][is_active]" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" checked>
                                Active slide item
                            </label>
                        </aside>
                    </div>
                </article>
            </template>
        </section>

        <div>
            <label for="caption" class="text-sm font-medium text-slate-900 dark:text-stone-200">Caption</label>
            <textarea id="caption" name="caption" rows="6" class="cms-textarea mt-2">{{ old('caption', $slider->caption) }}</textarea>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="primary_button_text" class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button text</label>
                <input id="primary_button_text" name="primary_button_text" type="text" value="{{ old('primary_button_text', $slider->primary_button_text) }}" class="cms-input mt-2">
            </div>
            <div>
                <label for="primary_button_link" class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button link</label>
                <input id="primary_button_link" name="primary_button_link" type="text" value="{{ old('primary_button_link', $slider->primary_button_link) }}" class="cms-input mt-2" placeholder="#features">
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="secondary_button_text" class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button text</label>
                <input id="secondary_button_text" name="secondary_button_text" type="text" value="{{ old('secondary_button_text', $slider->secondary_button_text) }}" class="cms-input mt-2">
            </div>
            <div>
                <label for="secondary_button_link" class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button link</label>
                <input id="secondary_button_link" name="secondary_button_link" type="text" value="{{ old('secondary_button_link', $slider->secondary_button_link) }}" class="cms-input mt-2" placeholder="#contact">
            </div>
        </div>
    </section>

    <aside class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="image_upload" class="text-sm font-medium text-slate-900 dark:text-stone-200">Fallback / cover image</label>
            <input id="image_upload" name="image_upload" type="file" accept="image/*" class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300">
            <p class="mt-2 text-xs text-slate-500 dark:text-stone-400">Used as a cover image in the CMS and as a fallback when a slide item has no image.</p>
            @if ($slider->imageUrl())
                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200/70 bg-white/70 p-2 dark:border-white/10 dark:bg-slate-950/30">
                    <img src="{{ $slider->imageUrl() }}" alt="{{ $slider->title }}" class="h-48 w-full rounded-xl object-cover">
                </div>
            @endif
        </div>

        <div>
            <label for="sort_order" class="text-sm font-medium text-slate-900 dark:text-stone-200">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $slider->sort_order ?? 0) }}" class="cms-input mt-2">
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('is_active', $slider->is_active ?? true))>
            Active slide
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
    </aside>
</div>

@once
    <script>
        (() => {
            const setupSliderItemManager = (manager) => {
                const list = manager.querySelector('[data-slider-item-list]');
                const addButton = manager.querySelector('[data-slider-item-add]');
                const template = manager.querySelector('template[data-slider-item-template]');

                if (!list || !addButton || !template) {
                    return;
                }

                const renumber = () => {
                    list.querySelectorAll('[data-slider-item-card]').forEach((card, index) => {
                        const label = card.querySelector('[data-slider-item-label]');

                        if (label) {
                            label.textContent = 'Slide item ' + (index + 1);
                        }
                    });
                };

                list.addEventListener('click', (event) => {
                    const removeButton = event.target.closest('[data-slider-item-remove]');

                    if (!removeButton) {
                        return;
                    }

                    const card = removeButton.closest('[data-slider-item-card]');

                    if (!card) {
                        return;
                    }

                    card.remove();
                    renumber();
                });

                addButton.addEventListener('click', () => {
                    const nextIndex = Number(list.dataset.nextIndex || '0');
                    const markup = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));

                    list.insertAdjacentHTML('beforeend', markup);
                    list.dataset.nextIndex = String(nextIndex + 1);
                    renumber();
                });

                renumber();
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('[data-slider-item-manager]').forEach(setupSliderItemManager);
                }, { once: true });
            } else {
                document.querySelectorAll('[data-slider-item-manager]').forEach(setupSliderItemManager);
            }
        })();
    </script>
@endonce