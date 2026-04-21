<x-layouts.site :title="$quiz->title.' | Quizzes | SRHR Connect'">
    <section class="mx-auto max-w-6xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <a href="{{ route('public.quizzes.index') }}" class="text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Back to quizzes</a>

        <article class="mt-4 rounded-[2rem] border border-white/10 bg-white/5 p-8">
            <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                <span>{{ ucfirst($quiz->audience) }}</span>
                <span>{{ $quiz->questions->count() }} questions</span>
            </div>
            <h1 class="mt-4 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">{{ $quiz->title }}</h1>
            @if ($quiz->summary)
                <p class="mt-4 text-lg leading-8 text-stone-300">{{ $quiz->summary }}</p>
            @endif
            @if ($quiz->intro_text)
                <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-400">{{ $quiz->intro_text }}</p>
            @endif

            <div class="mt-8 space-y-5">
                @foreach ($quiz->questions as $question)
                    <section class="rounded-3xl border border-white/10 bg-stone-950/40 p-6">
                        <p class="text-xs uppercase tracking-[0.3em] text-orange-200">Question {{ $loop->iteration }}</p>
                        <h2 class="mt-3 text-2xl font-semibold text-white">{{ $question->prompt }}</h2>
                        @if ($question->help_text)
                            <p class="mt-3 text-sm leading-6 text-stone-400">{{ $question->help_text }}</p>
                        @endif

                        <div class="mt-5 grid gap-3 md:grid-cols-2">
                            @foreach ($question->options as $option)
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="font-medium text-white">{{ $option->option_text }}</p>
                                    @if ($option->feedback)
                                        <p class="mt-2 text-sm leading-6 text-stone-400">{{ $option->feedback }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </article>
    </section>
</x-layouts.site>