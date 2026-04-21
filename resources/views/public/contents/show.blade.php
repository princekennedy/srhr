<x-layouts.site :title="$content->title.' | SRHR Connect'">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <a href="{{ route('public.contents.index') }}" class="text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Back to content</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <article class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                    <span>{{ $content->content_type }}</span>
                    <span>{{ $content->category?->name ?? 'General' }}</span>
                    <span>{{ ucfirst($content->audience) }}</span>
                </div>
                <h1 class="mt-4 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">{{ $content->title }}</h1>
                @if ($content->summary)
                    <p class="mt-4 text-lg leading-8 text-stone-300">{{ $content->summary }}</p>
                @endif

                <div class="article-content mt-8 space-y-6 text-base leading-8 text-stone-300">
                    <div>{!! $content->body !!}</div>

                    @foreach ($content->blocks as $block)
                        <section class="rounded-3xl border border-white/10 bg-stone-950/40 p-5">
                            @if ($block->title)
                                <h2 class="text-2xl font-semibold text-white">{{ $block->title }}</h2>
                            @endif
                            @if ($block->body)
                                <p class="mt-3 whitespace-pre-line text-stone-300">{{ $block->body }}</p>
                            @endif
                        </section>
                    @endforeach
                </div>
            </article>

            <aside class="space-y-5">
                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Page Context</p>
                    <div class="mt-4 space-y-3 text-sm text-stone-300">
                        <p><span class="font-semibold text-white">Audience:</span> {{ ucfirst($content->audience) }}</p>
                        <p><span class="font-semibold text-white">Category:</span> {{ $content->category?->name ?? 'General' }}</p>
                        @if ($content->published_at)
                            <p><span class="font-semibold text-white">Published:</span> {{ $content->published_at->format('M j, Y') }}</p>
                        @endif
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <p class="text-xs uppercase tracking-[0.3em] text-orange-200">Related Content</p>
                    <div class="mt-4 space-y-3">
                        @forelse ($relatedContents as $relatedContent)
                            <a href="{{ route('public.contents.show', $relatedContent) }}" class="block rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 transition hover:border-emerald-300/40 hover:bg-stone-950/60">
                                <p class="font-semibold text-white">{{ $relatedContent->title }}</p>
                                <p class="mt-1 text-sm text-stone-400">{{ $relatedContent->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $relatedContent->body), 80) }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-stone-500">No related public content is available yet.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </section>
</x-layouts.site>