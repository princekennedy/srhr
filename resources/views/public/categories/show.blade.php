<x-layouts.site :title="$category->name.' | Topics | SRHR Connect'">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <a href="{{ route('public.categories.index') }}" class="text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Back to topics</a>

        <div class="mt-4 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.35em] text-orange-200">Topic Detail</p>
            <h1 class="mt-3 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">{{ $category->name }}</h1>
            <p class="mt-4 text-lg leading-8 text-stone-300">{{ $category->description ?: 'Published content for this topic is available below.' }}</p>
        </div>

        <div class="mt-8 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
            @forelse ($contents as $content)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                        <span>{{ $content->content_type }}</span>
                        @if ($content->published_at)
                            <span>{{ $content->published_at->format('M j, Y') }}</span>
                        @endif
                    </div>
                    <h2 class="mt-3 text-2xl font-semibold text-white">{{ $content->title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-stone-400">{{ $content->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $content->body), 140) }}</p>
                    <a href="{{ route('public.contents.show', $content) }}" class="mt-5 inline-flex text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Read page</a>
                </article>
            @empty
                <article class="rounded-[2rem] border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 lg:col-span-2 xl:col-span-3">
                    No published content is available in this topic yet.
                </article>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $contents->links() }}
        </div>
    </section>
</x-layouts.site>