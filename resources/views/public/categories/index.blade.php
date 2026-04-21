<x-layouts.site title="Topics | SRHR Connect">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <div class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Public Topics</p>
            <h1 class="mt-3 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Browse published SRHR topics.</h1>
            <p class="mt-4 text-lg leading-8 text-stone-300">Every topic below leads to the same published content available in the mobile app. No login is required.</p>
        </div>

        <div class="mt-8 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
            @forelse ($categories as $category)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-2xl font-semibold text-white">{{ $category->name }}</h2>
                        <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-stone-300">{{ $category->contents_count }} pages</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-400">{{ $category->description ?: 'Published content grouped under this SRHR topic.' }}</p>

                    <div class="mt-5 space-y-3">
                        @forelse ($category->contents as $content)
                            <a href="{{ route('public.contents.show', $content) }}" class="block rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 transition hover:border-emerald-300/40 hover:bg-stone-950/60">
                                <p class="text-sm font-semibold text-white">{{ $content->title }}</p>
                                <p class="mt-1 text-sm text-stone-400">{{ $content->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $content->body), 90) }}</p>
                            </a>
                        @empty
                            <p class="rounded-2xl border border-dashed border-white/10 px-4 py-3 text-sm text-stone-500">No published pages in this topic yet.</p>
                        @endforelse
                    </div>

                    <a href="{{ route('public.categories.show', $category) }}" class="mt-5 inline-flex text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Open topic</a>
                </article>
            @empty
                <article class="rounded-[2rem] border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 lg:col-span-2 xl:col-span-3">
                    No public categories are available yet.
                </article>
            @endforelse
        </div>
    </section>
</x-layouts.site>