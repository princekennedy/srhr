<x-cms.layouts.app title="Content" eyebrow="CMS Content" heading="Content library" subheading="Manage reusable pages and educational content that the Android app can render dynamically.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.contents'))
        <x-slot:headerAction>
            <a href="{{ route('cms.contents.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New content</a>
        </x-slot:headerAction>
    @endif

    <div class="space-y-4">
        @forelse ($contents as $content)
            <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                            <span>{{ $content->content_type }}</span>
                            <span class="rounded-full border border-white/10 px-3 py-1">{{ $content->status }}</span>
                            <span class="rounded-full border border-white/10 px-3 py-1">{{ $content->audience }}</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-semibold text-white">{{ $content->title }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ $content->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $content->body), 180) }}</p>
                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-stone-400">
                            <span>Category: {{ $content->category?->name ?? 'Unassigned' }}</span>
                            <span>Slug: {{ $content->slug }}</span>
                            <span>Blocks: {{ $content->blocks->count() }}</span>
                        </div>
                    </div>

                    @if (auth()->user()?->hasCmsPermission('cms.manage.contents'))
                        <div class="flex gap-3">
                            <a href="{{ route('cms.contents.edit', $content) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Edit</a>
                            <form method="POST" action="{{ route('cms.contents.destroy', $content) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-medium text-rose-200" onclick="return confirm('Delete this content entry?');">Delete</button>
                            </form>
                        </div>
                    @else
                        <span class="text-sm font-medium text-stone-400">Read only</span>
                    @endif
                </div>
            </article>
        @empty
            <article class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400">
                No content entries yet.
            </article>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $contents->links() }}
    </div>
</x-cms.layouts.app>