<x-cms.layouts.app title="Dashboard" eyebrow="CMS Overview" heading="Delivery dashboard" subheading="This CMS now covers content, FAQs, quizzes, service directories, menus, and runtime settings aligned to the SRHR app scope in the technical documents.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.contents'))
        <x-slot:headerAction>
            <a href="{{ route('cms.contents.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">Create content</a>
        </x-slot:headerAction>
    @endif

    <section class="mb-6 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">Access mode</p>
            <h3 class="mt-3 text-2xl font-semibold text-white">{{ auth()->user()?->canManageAnyCmsModule() ? 'Management access enabled' : 'Read-only workspace' }}</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-400">
                {{ auth()->user()?->canManageAnyCmsModule() ? 'This account can create and update content, menus, and app settings based on assigned permissions.' : 'This account can review the dashboard, existing content, and navigation structure but cannot add or change records.' }}
            </p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">Quick actions</p>
            <div class="mt-4 flex flex-wrap gap-3">
                @if (auth()->user()?->hasCmsPermission('cms.manage.contents'))
                    <a href="{{ route('cms.contents.create') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">New content</a>
                @endif
                @if (auth()->user()?->hasCmsPermission('cms.manage.menus'))
                    <a href="{{ route('cms.menus.index') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Manage menus</a>
                @endif
                @if (auth()->user()?->hasCmsPermission('cms.manage.settings'))
                    <a href="{{ route('cms.settings.index') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Update settings</a>
                @endif
                @unless (auth()->user()?->canManageAnyCmsModule())
                    <span class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-stone-400">No write actions available</span>
                @endunless
            </div>
        </article>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Categories</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['categories'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Content Entries</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['contents'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">FAQs</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['faqs'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Quizzes</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['quizzes'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Services</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['services'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Menus</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['menus'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Menu Items</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['menuItems'] }}</p>
        </article>
        <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-stone-400">Settings</p>
            <p class="mt-3 text-4xl font-semibold text-white">{{ $stats['settings'] }}</p>
        </article>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
        <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Recent content</h3>
                    <p class="text-sm text-stone-400">The last edited entries ready for mobile rendering and publishing workflows.</p>
                </div>
                <a href="{{ route('cms.contents.index') }}" class="text-sm font-medium text-emerald-300">View all</a>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                    <thead class="bg-white/5 text-stone-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Title</th>
                            <th class="px-4 py-3 font-medium">Category</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($recentContents as $content)
                            <tr class="bg-stone-950/40 text-stone-200">
                                <td class="px-4 py-3 font-medium text-white">{{ $content->title }}</td>
                                <td class="px-4 py-3">{{ $content->category?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-stone-300">{{ $content->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-stone-400">{{ $content->updated_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-stone-400">No content entries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <h3 class="text-lg font-semibold text-white">Module highlights</h3>
            <div class="mt-4 space-y-3 text-sm leading-6 text-stone-300">
                @foreach ($moduleHighlights as $highlight)
                    <div class="rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3">
                        <div class="flex items-center justify-between gap-4">
                            <p class="font-medium text-white">{{ $highlight['label'] }}</p>
                            <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-emerald-200">{{ $highlight['count'] }}</span>
                        </div>
                        <p class="mt-2 text-stone-400">{{ $highlight['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
</x-cms.layouts.app>