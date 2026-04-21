<x-cms.layouts.app title="Categories" eyebrow="CMS Taxonomy" heading="Content categories" subheading="Organize SRHR topics for app navigation, discovery, and permissions-aware publishing.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.categories'))
        <x-slot:headerAction>
            <a href="{{ route('cms.categories.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New category</a>
        </x-slot:headerAction>
    @endif

    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5">
        <table class="min-w-full divide-y divide-white/10 text-left text-sm">
            <thead class="bg-white/5 text-stone-400">
                <tr>
                    <th class="px-5 py-4 font-medium">Name</th>
                    <th class="px-5 py-4 font-medium">Slug</th>
                    <th class="px-5 py-4 font-medium">Entries</th>
                    <th class="px-5 py-4 font-medium">Status</th>
                    <th class="px-5 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($categories as $category)
                    <tr class="bg-stone-950/40 text-stone-200">
                        <td class="px-5 py-4">
                            <p class="font-medium text-white">{{ $category->name }}</p>
                            @if ($category->description)
                                <p class="mt-1 text-xs text-stone-400">{{ \Illuminate\Support\Str::limit($category->description, 80) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-stone-400">{{ $category->slug }}</td>
                        <td class="px-5 py-4">{{ $category->contents_count }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $category->is_active ? 'text-emerald-200' : 'text-stone-400' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if (auth()->user()?->hasCmsPermission('cms.manage.categories'))
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('cms.categories.edit', $category) }}" class="text-sm font-medium text-emerald-300">Edit</a>
                                    <form method="POST" action="{{ route('cms.categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-rose-300" onclick="return confirm('Delete this category?');">Delete</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-sm font-medium text-stone-400">Read only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-stone-400">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-cms.layouts.app>