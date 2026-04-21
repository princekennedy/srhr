<x-cms.layouts.app title="Menus" eyebrow="CMS Navigation" heading="Menu builder" subheading="Create database-driven navigation structures that the app can request and render dynamically.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.menus'))
        <x-slot:headerAction>
            <a href="{{ route('cms.menus.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New menu</a>
        </x-slot:headerAction>
    @endif

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse ($menus as $menu)
            <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-semibold text-white">{{ $menu->name }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ $menu->description ?: 'No description yet.' }}</p>
                    </div>
                    <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $menu->is_active ? 'text-emerald-200' : 'text-stone-400' }}">{{ $menu->is_active ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-4 text-sm text-stone-400">
                    <span>Slug: {{ $menu->slug }}</span>
                    <span>Location: {{ $menu->location ?: 'Not set' }}</span>
                    <span>Items: {{ $menu->items_count }}</span>
                </div>

                <div class="mt-6 flex gap-3">
                    @if (auth()->user()?->hasCmsPermission('cms.manage.menus'))
                        <a href="{{ route('cms.menus.edit', $menu) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Open builder</a>
                        <form method="POST" action="{{ route('cms.menus.destroy', $menu) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-medium text-rose-200" onclick="return confirm('Delete this menu and its items?');">Delete</button>
                        </form>
                    @else
                        <span class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-stone-400">Read only</span>
                    @endif
                </div>
            </article>
        @empty
            <article class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 xl:col-span-2">
                No menus yet.
            </article>
        @endforelse
    </div>
</x-cms.layouts.app>