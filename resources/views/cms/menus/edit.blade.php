<x-cms.layouts.app title="Edit Menu" eyebrow="CMS Navigation" heading="Edit menu" subheading="Update menu metadata and maintain the navigation items that will drive the mobile experience.">
    <x-slot:headerAction>
        <a href="{{ route('cms.menus.items.create', $menu) }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">Add item</a>
    </x-slot:headerAction>

    <form method="POST" action="{{ route('cms.menus.update', $menu) }}">
        @csrf
        @method('PUT')
        @include('cms.menus._form', ['submitLabel' => 'Update menu'])
    </form>

    <section class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-white">Menu items</h3>
                <p class="text-sm text-stone-400">Each item can target content, categories, routes, or externally managed web pages.</p>
            </div>
            <a href="{{ route('cms.menus.items.create', $menu) }}" class="text-sm font-medium text-emerald-300">Create item</a>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                <thead class="bg-white/5 text-stone-400">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Type</th>
                        <th class="px-4 py-3 font-medium">Target</th>
                        <th class="px-4 py-3 font-medium">Flags</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($menu->items as $item)
                        <tr class="bg-stone-950/40 text-stone-200">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">{{ $item->title }}</p>
                                <p class="mt-1 text-xs text-stone-500">Sort order: {{ $item->sort_order }}{{ $item->parent ? ' | Child of '.$item->parent->title : '' }}</p>
                            </td>
                            <td class="px-4 py-3 uppercase tracking-[0.15em] text-stone-400">{{ $item->type }}</td>
                            <td class="px-4 py-3 text-stone-400">{{ $item->target_reference ?: ($item->route ?: 'None') }}</td>
                            <td class="px-4 py-3 text-xs uppercase tracking-[0.15em] text-stone-400">{{ $item->is_active ? 'Active' : 'Inactive' }}{{ $item->open_in_webview ? ' | WebView' : '' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('cms.menus.items.edit', [$menu, $item]) }}" class="text-sm font-medium text-emerald-300">Edit</a>
                                    <form method="POST" action="{{ route('cms.menus.items.destroy', [$menu, $item]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-rose-300" onclick="return confirm('Delete this menu item?');">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-stone-400">No items in this menu yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-cms.layouts.app>