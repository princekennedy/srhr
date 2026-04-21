<x-layouts.site title="SRHR Connect">
    <section class="mx-auto grid max-w-7xl gap-8 px-5 pb-10 pt-6 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:px-10 lg:pb-16 lg:pt-10">
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-300/25 bg-emerald-300/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-emerald-200">
                Youth-Centered Digital Health
            </div>

            <div class="space-y-5">
                <h1 class="max-w-4xl text-5xl font-semibold tracking-tight text-white sm:text-6xl" style="font-family: 'Figtree', sans-serif;">
                    A safer front door for SRHR information, services, and guided support.
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-stone-300">
                    SRHR Connect brings together youth-friendly information, structured referrals, and a secure content management workflow. The public web experience now exposes the same published guidance, FAQs, quizzes, and service listings that power the mobile app, without requiring sign-in.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @auth
                    @if (auth()->user()?->canAccessCms())
                        <a href="{{ route('cms.dashboard') }}" class="rounded-full bg-emerald-400 px-6 py-3.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Open CMS workspace</a>
                    @else
                        <a href="{{ route('public.contents.index') }}" class="rounded-full bg-emerald-400 px-6 py-3.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Browse public content</a>
                        <a href="{{ route('public.services.index') }}" class="rounded-full border border-white/15 bg-white/10 px-6 py-3.5 font-semibold text-white transition hover:border-white/25 hover:bg-white/15">Find services</a>
                    @endif
                @else
                    <a href="{{ route('public.contents.index') }}" class="rounded-full bg-emerald-400 px-6 py-3.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Browse public content</a>
                    <a href="{{ route('register') }}" class="rounded-full border border-white/15 bg-white/10 px-6 py-3.5 font-semibold text-white transition hover:border-white/25 hover:bg-white/15">Create account</a>
                @endauth
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-sm text-stone-400">Published content</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $featuredContents->count() }}</p>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-sm text-stone-400">Knowledge tracks</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $categories->count() }}</p>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-sm text-stone-400">Navigation items</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $primaryMenuItems->count() }}</p>
                </article>
            </div>
        </div>

        <div class="grid gap-4">
            <article class="overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-teal-400/20 via-cyan-300/10 to-orange-400/15 p-6 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Platform Shape</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-stone-950/60 p-5">
                        <p class="text-sm font-semibold text-white">Public experience</p>
                        <p class="mt-2 text-sm leading-6 text-stone-300">Published pages, topic browsing, FAQs, quizzes, and service discovery are open to every visitor.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-stone-950/60 p-5">
                        <p class="text-sm font-semibold text-white">Protected CMS</p>
                        <p class="mt-2 text-sm leading-6 text-stone-300">Structured content, menu management, and app-facing configuration stay reserved for administrator accounts.</p>
                    </div>
                </div>
                <div class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-5">
                    <p class="text-sm text-stone-300">Starter navigation</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse (($primaryMenuItems ?? collect()) as $item)
                            @if (filled($item['href'] ?? null))
                                <a href="{{ $item['href'] }}" class="rounded-full border border-white/10 px-3 py-1.5 text-sm text-stone-200 transition hover:border-emerald-300/40 hover:text-emerald-200">{{ $item['title'] }}</a>
                            @else
                                <span class="rounded-full border border-white/10 px-3 py-1.5 text-sm text-stone-200">{{ $item['title'] }}</span>
                            @endif
                        @empty
                            <span class="text-sm text-stone-400">Run the seeders to populate menu links.</span>
                        @endforelse
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-2 sm:px-8 lg:px-10">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('public.categories.index') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5 transition hover:border-emerald-300/40 hover:bg-white/10">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Topics</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Browse by subject</h2>
                <p class="mt-2 text-sm leading-6 text-stone-400">Move through published SRHR content by category.</p>
            </a>
            <a href="{{ route('public.contents.index') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5 transition hover:border-emerald-300/40 hover:bg-white/10">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Content</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Read public guidance</h2>
                <p class="mt-2 text-sm leading-6 text-stone-400">Open the same published learning content exposed to the mobile app.</p>
            </a>
            <a href="{{ route('public.faqs.index') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5 transition hover:border-emerald-300/40 hover:bg-white/10">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">FAQs</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Get quick answers</h2>
                <p class="mt-2 text-sm leading-6 text-stone-400">Review short, trusted responses to common questions.</p>
            </a>
            <a href="{{ route('public.services.index') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5 transition hover:border-emerald-300/40 hover:bg-white/10">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Services</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Find support</h2>
                <p class="mt-2 text-sm leading-6 text-stone-400">Locate public referral and youth-friendly service entries without signing in.</p>
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10">
        <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                <p class="text-xs uppercase tracking-[0.35em] text-orange-200">Priority Areas</p>
                <div class="mt-5 space-y-4">
                    @forelse ($categories as $category)
                        <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <h2 class="text-lg font-semibold text-white">{{ $category->name }}</h2>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-stone-300">{{ $category->contents_count }} items</span>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-stone-400">{{ $category->description }}</p>
                        </div>
                    @empty
                        <p class="text-stone-400">No categories yet.</p>
                    @endforelse
                </div>
            </article>

            <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Featured Content</p>
                        <h2 class="mt-2 text-3xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Published SRHR guidance ready for delivery</h2>
                    </div>
                </div>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @forelse ($featuredContents as $content)
                        <article class="rounded-3xl border border-white/10 bg-stone-950/40 p-5">
                            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.25em] text-stone-400">
                                <span>{{ $content->content_type }}</span>
                                <span>{{ $content->category?->name ?? 'General' }}</span>
                            </div>
                            <h3 class="mt-3 text-xl font-semibold text-white">{{ $content->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-stone-400">{{ $content->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $content->body), 130) }}</p>
                            <a href="{{ route('public.contents.show', $content) }}" class="mt-4 inline-flex text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Read page</a>
                        </article>
                    @empty
                        <p class="text-stone-400 md:col-span-2">No published content yet. Run the seeders to populate the frontend.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
</x-layouts.site>
