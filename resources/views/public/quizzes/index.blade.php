<x-layouts.site title="Quizzes | SRHR Connect">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <div class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Public Quizzes</p>
            <h1 class="mt-3 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Interactive public quizzes.</h1>
            <p class="mt-4 text-lg leading-8 text-stone-300">Review the same published learning quizzes exposed through the mobile experience.</p>
        </div>

        <div class="mt-8 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
            @forelse ($quizzes as $quiz)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                        <span>{{ ucfirst($quiz->audience) }}</span>
                        <span>{{ $quiz->questions_count }} questions</span>
                    </div>
                    <h2 class="mt-3 text-2xl font-semibold text-white">{{ $quiz->title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-stone-400">{{ $quiz->summary ?: 'Published public quiz.' }}</p>
                    <a href="{{ route('public.quizzes.show', $quiz) }}" class="mt-5 inline-flex text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Open quiz</a>
                </article>
            @empty
                <article class="rounded-[2rem] border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 lg:col-span-2 xl:col-span-3">
                    No public quizzes are available yet.
                </article>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $quizzes->links() }}
        </div>
    </section>
</x-layouts.site>