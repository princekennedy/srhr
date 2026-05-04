<x-layouts.app title="Content" eyebrow="CMS Content" heading="Content library" subheading="Manage root categories and child content entries from one unified content workspace.">
    @php
        $contentLayoutOptions = \App\Support\DesignLayouts::contentOptions();
        $categoryLayoutOptions = \App\Support\DesignLayouts::categoryOptions();
        $canManageCategories = auth()->user()?->hasCmsPermission('cms.manage.categories');
        $canManageContents = auth()->user()?->hasCmsPermission('cms.manage.contents');
    @endphp

    @if ($canManageCategories || $canManageContents)
        <x-slot:headerAction>
            <div class="flex flex-wrap items-center gap-3">
                @if ($canManageCategories)
                    <a href="{{ route('cms.contents.create', ['kind' => 'category']) }}" class="inline-flex items-center rounded-full border border-sky-200 bg-white/80 px-5 py-3 text-sm font-semibold text-sky-700 transition hover:-translate-y-0.5 hover:border-sky-300 hover:text-sky-900 dark:border-sky-400/30 dark:bg-white/5 dark:text-sky-200 dark:hover:text-white">New category</a>
                @endif
                @if ($canManageContents)
                    <a href="{{ route('cms.contents.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">New content</a>
                @endif
            </div>
        </x-slot:headerAction>
    @endif

    <x-cms.list-view-switcher storage-key="cms:list-view:contents" target-id="cms-listing-contents" default="card" />

    <div id="cms-listing-contents">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Categories and content</h2>
                <p class="text-sm text-slate-500 dark:text-stone-400">Each category stays in the same listing and shows its child content items inline.</p>
            </div>
            <span class="cms-chip px-3 py-1 text-xs uppercase tracking-[0.2em]">{{ $categories->total() }} total</span>
        </div>

        <div data-view-panel="table" class="hidden">
            <div class="cms-table-wrap">
                <table class="min-w-full divide-y divide-slate-200/70 text-left text-sm dark:divide-white/10">
                    <thead class="bg-white/50 text-slate-500 dark:bg-white/5 dark:text-stone-400">
                        <tr>
                            <th class="px-5 py-4 font-medium">Category</th>
                            <th class="px-5 py-4 font-medium">Children</th>
                            <th class="px-5 py-4 font-medium">Status</th>
                            <th class="px-5 py-4 font-medium">Layout</th>
                            <th class="px-5 py-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($categories as $category)
                            <tr class="bg-white/70 text-slate-700 dark:bg-slate-950/30 dark:text-stone-200">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-stone-400">{{ $category->description ?: 'No description written yet.' }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">
                                        <span class="cms-chip px-2 py-1">{{ $category->visibility }}</span>
                                        <span>{{ $category->contents_count }} item{{ $category->contents_count === 1 ? '' : 's' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-stone-400">
                                    @if ($category->contents->isNotEmpty())
                                        <div class="space-y-3">
                                            @foreach ($category->contents as $childContent)
                                                <div class="rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div>
                                                            <p class="font-medium text-slate-900 dark:text-white">{{ $childContent->title }}</p>
                                                            <p class="mt-1 text-xs text-slate-500 dark:text-stone-400">{{ \Illuminate\Support\Str::limit($childContent->summary ?: strip_tags((string) $childContent->body), 100) }}</p>
                                                            <div class="mt-2 flex flex-wrap gap-3 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">
                                                                <span>{{ $childContent->content_type }}</span>
                                                                <span>{{ $childContent->status }}</span>
                                                                <span>{{ $childContent->blocks->count() }} block{{ $childContent->blocks->count() === 1 ? '' : 's' }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="cms-action-group cms-action-group--end shrink-0">
                                                            <x-cms.layout-preview-launcher
                                                                section="content"
                                                                :layout="$childContent->normalizedLayoutType()"
                                                                :options="$contentLayoutOptions"
                                                                :params="['content_id' => $childContent->id]"
                                                                title="Content Layout Preview"
                                                                button-label="Preview"
                                                            >
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                            </x-cms.layout-preview-launcher>
                                                            @if ($canManageContents)
                                                                <a href="{{ route('cms.contents.edit', $childContent) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Edit">
                                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                                                </a>
                                                                <form method="POST" action="{{ route('cms.contents.destroy', $childContent) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="cms-action-btn cms-action-btn-sm cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this content entry?');">
                                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.11 0 0 0-7.5 0" /></svg>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 dark:text-stone-400">No child content yet.</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ $category->is_active ? 'active' : 'inactive' }}</td>
                                <td class="px-5 py-4 text-slate-500 dark:text-stone-400">{{ $category->normalizedLayoutType() }}</td>
                                <td class="px-5 py-4">
                                    <div class="cms-action-group cms-action-group--end">
                                        <x-cms.layout-preview-launcher
                                            section="categories"
                                            :layout="$category->normalizedLayoutType()"
                                            :options="$categoryLayoutOptions"
                                            :params="['category_id' => $category->id]"
                                            title="Category Layout Preview"
                                            button-label="Preview"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </x-cms.layout-preview-launcher>
                                        @if ($canManageCategories)
                                            <a href="{{ route('cms.contents.edit', ['content' => $category, 'tab' => 'children']) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Manage category">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                            </a>
                                            <form method="POST" action="{{ route('cms.contents.destroy', $category) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="cms-action-btn cms-action-btn-sm cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this category?');">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500 dark:text-stone-400">No content entries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div data-view-panel="card" class="space-y-4">
            @forelse ($categories as $category)
                <article class="cms-card cms-gradient-card cms-card-hover p-6">
                    <div class="flex flex-col gap-5 border-b border-slate-200/70 pb-5 dark:border-white/10 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">
                                <span>category</span>
                                <span class="cms-chip px-3 py-1">{{ $category->visibility }}</span>
                                <span>{{ $category->contents_count }} item{{ $category->contents_count === 1 ? '' : 's' }}</span>
                            </div>
                            <h3 class="cms-heading mt-3 text-2xl font-semibold">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-slate-600 dark:text-stone-300">{{ $category->description ?: 'No description written yet.' }}</p>
                            <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500 dark:text-stone-400">
                                <span>Slug: {{ $category->slug }}</span>
                                <span>Layout: {{ $category->normalizedLayoutType() }}</span>
                                <span>Status: {{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>

                        <div class="cms-action-group">
                            <x-cms.layout-preview-launcher
                                section="categories"
                                :layout="$category->normalizedLayoutType()"
                                :options="$categoryLayoutOptions"
                                :params="['category_id' => $category->id]"
                                title="Category Layout Preview"
                                button-label="Preview"
                                button-class="cms-action-btn cms-action-btn-md cms-action-btn--preview"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </x-cms.layout-preview-launcher>

                            @if ($canManageCategories)
                                <a href="{{ route('cms.contents.edit', ['content' => $category, 'tab' => 'children']) }}" class="cms-action-btn cms-action-btn-md cms-action-btn--edit" title="Manage category">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                </a>
                                <form method="POST" action="{{ route('cms.contents.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cms-action-btn cms-action-btn-md cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this category?');">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-sm font-medium text-slate-500 dark:text-stone-400">Read only</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($category->contents as $childContent)
                            <article class="rounded-2xl border border-slate-200/70 bg-white/70 p-5 dark:border-white/10 dark:bg-slate-950/30">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="max-w-3xl">
                                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">
                                            <span>{{ $childContent->content_type }}</span>
                                            <span class="cms-chip px-3 py-1">{{ $childContent->status }}</span>
                                            <span class="cms-chip px-3 py-1">{{ $childContent->audience }}</span>
                                        </div>
                                        <h4 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ $childContent->title }}</h4>
                                        <p class="mt-2 text-sm text-slate-600 dark:text-stone-300">{{ $childContent->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $childContent->body), 180) }}</p>
                                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500 dark:text-stone-400">
                                            <span>Slug: {{ $childContent->slug }}</span>
                                            <span>Layout: {{ $childContent->normalizedLayoutType() }}</span>
                                            <span>Blocks: {{ $childContent->blocks->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="cms-action-group">
                                        <x-cms.layout-preview-launcher
                                            section="content"
                                            :layout="$childContent->normalizedLayoutType()"
                                            :options="$contentLayoutOptions"
                                            :params="['content_id' => $childContent->id]"
                                            title="Content Layout Preview"
                                            button-label="Preview"
                                            button-class="cms-action-btn cms-action-btn-md cms-action-btn--preview"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </x-cms.layout-preview-launcher>

                                        @if ($canManageContents)
                                            <a href="{{ route('cms.contents.edit', $childContent) }}" class="cms-action-btn cms-action-btn-md cms-action-btn--edit" title="Edit">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                            </a>
                                            <form method="POST" action="{{ route('cms.contents.destroy', $childContent) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="cms-action-btn cms-action-btn-md cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this content entry?');">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.11 0 0 0-7.5 0" /></svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-sm font-medium text-slate-500 dark:text-stone-400">Read only</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <article class="cms-empty-state p-8 text-center">
                                No child content items in this category yet.
                            </article>
                        @endforelse
                    </div>
                </article>
            @empty
                <article class="cms-empty-state p-10 text-center">
                    No categories yet.
                </article>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
</x-layouts.app>