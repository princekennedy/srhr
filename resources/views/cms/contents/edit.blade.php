<x-layouts.app :title="$content->isCategory() ? 'Edit Category' : 'Edit Content'" eyebrow="CMS Content" :heading="$content->isCategory() ? 'Edit category' : 'Edit content'" :subheading="$content->isCategory() ? 'Manage a root category record, its children, and the public category preview from one place.' : 'Update publication state, audience, and rich text body content before it reaches the public website and mobile app.'">
    @php
        $contentLayoutOptions = \App\Support\DesignLayouts::contentOptions();
        $categoryLayoutOptions = \App\Support\DesignLayouts::categoryOptions();
        $activeTab = request()->query('tab', 'details');
        $activeTab = $content->isCategory()
            ? (in_array($activeTab, ['details', 'children', 'preview'], true) ? $activeTab : 'details')
            : (in_array($activeTab, ['details', 'parent', 'preview'], true) ? $activeTab : 'details');
        $publicContentUrl = $content->isCategory() ? route('public.categories.show', $content) : route('public.contents.show', $content);
    @endphp

    <div class="mb-6 flex gap-3">
        <a href="{{ $content->isCategory() ? route('cms.contents.index') : ($content->category_id ? route('cms.contents.edit', ['content' => $content->category_id, 'tab' => 'children']) : route('cms.contents.index')) }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-stone-400 dark:hover:text-stone-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back
        </a>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach (($content->isCategory() ? ['details' => 'Details', 'children' => 'Children', 'preview' => 'Preview'] : ['details' => 'Details', 'parent' => 'Parent', 'preview' => 'Preview']) as $tabKey => $tabLabel)
            <a
                href="{{ route('cms.contents.edit', ['content' => $content, 'tab' => $tabKey]) }}"
                class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === $tabKey ? 'bg-sky-500 text-white shadow-lg shadow-sky-200/40 dark:shadow-none' : 'border border-slate-200/70 bg-white/70 text-slate-600 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:text-white' }}"
            >
                {{ $tabLabel }}
            </a>
        @endforeach
    </div>

    @if ($content->isCategory())
        @if ($activeTab === 'details')
            <form method="POST" action="{{ route('cms.contents.update', ['content' => $content, 'tab' => 'details']) }}">
                @csrf
                @method('PUT')
                @include('cms.contents._category-form', ['submitLabel' => 'Update category'])
            </form>
        @elseif ($activeTab === 'children')
            <section class="rounded-3xl border border-slate-200/70 bg-white/50 p-6 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Content items</h3>
                        <p class="text-sm text-slate-500 dark:text-stone-400">Manage all content items that belong to this category.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-cms.layout-preview-launcher
                            section="categories"
                            :layout="$content->normalizedLayoutType()"
                            :options="$categoryLayoutOptions"
                            :params="['category_id' => $content->id]"
                            title="Category Layout Preview"
                            button-label="Preview Category"
                            button-class="inline-flex items-center rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600"
                        />
                        <a href="{{ route('cms.contents.create', ['category_id' => $content->id]) }}" class="text-sm font-medium text-sky-600 hover:text-sky-700 dark:text-sky-300">Add content</a>
                    </div>
                </div>

                <x-cms.list-view-switcher storage-key="cms:list-view:content-category-children" target-id="cms-listing-content-category-children" default="table" />

                <div id="cms-listing-content-category-children" class="mt-5">
                    <div data-view-panel="table" class="overflow-hidden rounded-2xl border border-slate-200/70 dark:border-white/10">
                        <table class="min-w-full divide-y divide-slate-200/70 text-left text-sm dark:divide-white/10">
                            <thead class="bg-white/50 text-slate-500 dark:bg-white/5 dark:text-stone-400">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Title</th>
                                    <th class="px-4 py-3 font-medium">Type</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                @forelse ($content->contents as $childContent)
                                    <tr class="bg-white/70 text-slate-700 dark:bg-slate-950/30 dark:text-stone-200">
                                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $childContent->title }}</td>
                                        <td class="px-4 py-3 capitalize text-slate-500 dark:text-stone-400">{{ $childContent->content_type }}</td>
                                        <td class="px-4 py-3 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ $childContent->status }}</td>
                                        <td class="px-4 py-3">
                                            <div class="cms-action-group cms-action-group--end">
                                                <x-cms.layout-preview-launcher
                                                    section="content"
                                                    :layout="$childContent->normalizedLayoutType()"
                                                    :options="$contentLayoutOptions"
                                                    :params="['content_id' => $childContent->id]"
                                                    title="Content Layout Preview"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                </x-cms.layout-preview-launcher>
                                                <a href="{{ route('cms.contents.edit', $childContent) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Edit">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                                </a>
                                                <form method="POST" action="{{ route('cms.contents.destroy', ['content' => $childContent, 'tab' => 'children']) }}" class="block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="cms-action-btn cms-action-btn-sm cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this content item?');">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-stone-400">No content items in this category yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div data-view-panel="card" class="hidden grid gap-4 md:grid-cols-2">
                        @forelse ($content->contents as $childContent)
                            <article class="rounded-2xl border border-slate-200/70 bg-white/70 p-5 dark:border-white/10 dark:bg-slate-950/30">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-semibold text-slate-900 dark:text-white">{{ $childContent->title }}</h4>
                                        <p class="mt-1 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ $childContent->content_type }} | {{ $childContent->status }}</p>
                                    </div>
                                    <div class="cms-action-group">
                                        <x-cms.layout-preview-launcher
                                            section="content"
                                            :layout="$childContent->normalizedLayoutType()"
                                            :options="$contentLayoutOptions"
                                            :params="['content_id' => $childContent->id]"
                                            title="Content Layout Preview"
                                            button-class="cms-action-btn cms-action-btn-sm cms-action-btn--preview"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </x-cms.layout-preview-launcher>
                                        <a href="{{ route('cms.contents.edit', $childContent) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <article class="cms-empty-state p-10 text-center md:col-span-2">No content items in this category yet.</article>
                        @endforelse
                    </div>
                </div>
            </section>
        @else
            <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <article class="cms-card cms-gradient-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Current experience</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $content->name }}</h3>
                            <p class="mt-3 text-sm text-slate-600 dark:text-stone-300">{{ $content->description ?: 'No description yet.' }}</p>
                        </div>
                        <x-cms.layout-preview-launcher
                            section="categories"
                            :layout="$content->normalizedLayoutType()"
                            :options="$categoryLayoutOptions"
                            :params="['category_id' => $content->id]"
                            title="Category Layout Preview"
                            button-label="Preview Category"
                            button-class="inline-flex items-center rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600"
                        />
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Public URL</p>
                            <a href="{{ $publicContentUrl }}" target="_blank" rel="noreferrer" class="mt-2 block text-sm font-medium text-sky-600 hover:text-sky-700 dark:text-sky-300">{{ $publicContentUrl }}</a>
                        </div>
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Children</p>
                            <p class="mt-2 text-sm text-slate-700 dark:text-stone-200">{{ $content->contents->count() }} content item{{ $content->contents->count() === 1 ? '' : 's' }}</p>
                        </div>
                    </div>
                </article>

                <article class="cms-card cms-gradient-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Hierarchy note</p>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">Categories are root content records</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-stone-300">This page owns the child content records shown in the Children tab. Use preview here to validate the public category layout before publishing.</p>
                </article>
            </section>
        @endif
    @elseif ($activeTab === 'details')
        <form method="POST" action="{{ route('cms.contents.update', ['content' => $content, 'tab' => 'details']) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('cms.contents._form', ['submitLabel' => 'Update content'])
        </form>

        <section class="mt-6 rounded-3xl border border-slate-200/70 bg-white/50 p-6 dark:border-white/10 dark:bg-white/5">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Block readiness</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-stone-300">The main body now uses CKEditor. This entry currently has {{ $content->blocks->count() }} block{{ $content->blocks->count() === 1 ? '' : 's' }} stored separately for a future block-management workflow.</p>
        </section>
    @elseif ($activeTab === 'parent')
        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="cms-card cms-gradient-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Hierarchy</p>
                <h3 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $content->category?->name ?? 'Unassigned category' }}</h3>
                <p class="mt-3 text-sm text-slate-600 dark:text-stone-300">Content entries are child records in the unified contents table. Their category relationship drives grouping, navigation, and parent-level previews.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Category</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-stone-200">{{ $content->category?->name ?? 'Not assigned' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Status</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-stone-200">{{ ucfirst($content->status) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Audience</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-stone-200">{{ ucfirst($content->audience) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Visibility</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-stone-200">{{ ucfirst($content->visibility) }}</p>
                    </div>
                </div>
            </article>

            <article class="cms-card cms-gradient-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Assets</p>
                <div class="mt-4 space-y-4 text-sm text-slate-600 dark:text-stone-300">
                    <p>Featured image: {{ $content->featuredImageUrl() ? 'Attached' : 'None' }}</p>
                    <p>Attachments: {{ $content->getMedia('attachments')->count() }}</p>
                    <p>Blocks stored: {{ $content->blocks->count() }}</p>
                    @if ($content->category)
                        <a href="{{ route('cms.contents.edit', ['content' => $content->category, 'tab' => 'children']) }}" class="inline-flex items-center rounded-full border border-slate-200/70 bg-white/70 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200 dark:hover:text-white">Open parent category</a>
                    @endif
                </div>
            </article>
        </section>
    @else
        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="cms-card cms-gradient-card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Current experience</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $content->title }}</h3>
                        <p class="mt-3 text-sm text-slate-600 dark:text-stone-300">{{ $content->summary ?: 'No summary written yet.' }}</p>
                    </div>
                    <x-cms.layout-preview-launcher
                        section="content"
                        :layout="$content->normalizedLayoutType()"
                        :options="$contentLayoutOptions"
                        :params="['content_id' => $content->id]"
                        title="Content Layout Preview"
                        button-label="Preview Content"
                        button-class="inline-flex items-center rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600"
                    />
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Public URL</p>
                        <a href="{{ $publicContentUrl }}" target="_blank" rel="noreferrer" class="mt-2 block text-sm font-medium text-sky-600 hover:text-sky-700 dark:text-sky-300">{{ $publicContentUrl }}</a>
                    </div>
                    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 dark:border-white/10 dark:bg-slate-950/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Layout</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-stone-200">{{ ucfirst($content->normalizedLayoutType()) }}</p>
                    </div>
                </div>
            </article>

            <article class="cms-card cms-gradient-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Publishing note</p>
                <h3 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">Preview validates the public rendering</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-stone-300">Use the preview launcher to check the actual design template for this child content record before linking it from menus or publishing it under its parent category.</p>
            </article>
        </section>
    @endif
</x-layouts.app>