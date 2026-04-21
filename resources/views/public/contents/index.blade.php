<x-layouts.site title="Content | SRHR Connect">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <div class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Public Library</p>
            <h1 class="mt-3 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Published content from the mobile app.</h1>
            <p class="mt-4 text-lg leading-8 text-stone-300">Browse the public pages, articles, referrals, and service-linked guidance already available to app users.</p>
        </div>

        <form method="GET" action="{{ route('public.contents.index') }}" class="mt-8 grid gap-4 rounded-[2rem] border border-white/10 bg-white/5 p-6 lg:grid-cols-[1.1fr_0.8fr_0.8fr_auto]">
            <div>
                <label for="q" class="text-sm font-medium text-stone-200">Search</label>
                <input id="q" name="q" type="text" value="{{ $search }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="Search title or summary">
            </div>
            <div>
                <label for="category" class="text-sm font-medium text-stone-200">Category</label>
                <select id="category" name="category" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="type" class="text-sm font-medium text-stone-200">Type</label>
                <select id="type" name="type" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                    <option value="">All types</option>
                    @foreach ($typeOptions as $option)
                        <option value="{{ $option }}" @selected($selectedType === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="rounded-full bg-emerald-400 px-5 py-3 font-semibold text-stone-950 transition hover:bg-emerald-300">Filter</button>
                <a href="{{ route('public.contents.index') }}" class="rounded-full border border-white/15 bg-white/10 px-5 py-3 font-semibold text-white transition hover:border-white/25 hover:bg-white/15">Reset</a>
            </div>
        </form>

        <div class="mt-8 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
            @forelse ($contents as $content)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                        <span>{{ $content->content_type }}</span>
                        <span>{{ $content->category?->name ?? 'General' }}</span>
                    </div>
                    <h2 class="mt-3 text-2xl font-semibold text-white">{{ $content->title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-stone-400">{{ $content->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $content->body), 140) }}</p>
                    <a href="{{ route('public.contents.show', $content) }}" class="mt-5 inline-flex text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Read page</a>
                </article>
            @empty
                <article class="rounded-[2rem] border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 lg:col-span-2 xl:col-span-3">
                    No public content matched the current filters.
                </article>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $contents->links() }}
        </div>
    </section>
</x-layouts.site>