<x-layouts.site title="FAQs | SRHR Connect">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <div class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Public FAQs</p>
            <h1 class="mt-3 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Trusted answers to common SRHR questions.</h1>
            <p class="mt-4 text-lg leading-8 text-stone-300">These published FAQ entries are available to visitors and mobile users without requiring login.</p>
        </div>

        <form method="GET" action="{{ route('public.faqs.index') }}" class="mt-8 flex flex-wrap items-end gap-4 rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="min-w-[260px] flex-1">
                <label for="category" class="text-sm font-medium text-stone-200">Category</label>
                <select id="category" name="category" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="rounded-full bg-emerald-400 px-5 py-3 font-semibold text-stone-950 transition hover:bg-emerald-300">Filter</button>
                <a href="{{ route('public.faqs.index') }}" class="rounded-full border border-white/15 bg-white/10 px-5 py-3 font-semibold text-white transition hover:border-white/25 hover:bg-white/15">Reset</a>
            </div>
        </form>

        <div class="mt-8 space-y-4">
            @forelse ($faqs as $faq)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                        <span>{{ $faq->category?->name ?? 'General' }}</span>
                        <span>{{ ucfirst($faq->audience) }}</span>
                    </div>
                    <h2 class="mt-3 text-2xl font-semibold text-white">{{ $faq->question }}</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-stone-300">{{ $faq->answer }}</p>
                </article>
            @empty
                <article class="rounded-[2rem] border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400">
                    No public FAQs matched the current filters.
                </article>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $faqs->links() }}
        </div>
    </section>
</x-layouts.site>