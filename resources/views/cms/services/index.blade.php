<x-cms.layouts.app title="Services" eyebrow="CMS Referrals" heading="Service directory" subheading="Manage youth-friendly facilities, referral points, and practical contact information for the app.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.services'))
        <x-slot:headerAction>
            <a href="{{ route('cms.services.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New service</a>
        </x-slot:headerAction>
    @endif

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse ($services as $service)
            <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                            <span>{{ $service->district ?: 'District pending' }}</span>
                            <span class="rounded-full border border-white/10 px-3 py-1">{{ $service->is_featured ? 'Featured' : 'Standard' }}</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-semibold text-white">{{ $service->name }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ $service->summary ?: 'No summary yet.' }}</p>
                    </div>
                    <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $service->is_active ? 'text-emerald-200' : 'text-stone-400' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="mt-4 space-y-2 text-sm text-stone-400">
                    <p>Category: {{ $service->category?->name ?? 'Unassigned' }}</p>
                    <p>Hours: {{ $service->service_hours ?: 'Not set' }}</p>
                    <p>Phone: {{ $service->contact_phone ?: 'Not set' }}</p>
                </div>

                @if (auth()->user()?->hasCmsPermission('cms.manage.services'))
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('cms.services.edit', $service) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Edit</a>
                        <form method="POST" action="{{ route('cms.services.destroy', $service) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-medium text-rose-200" onclick="return confirm('Delete this service entry?');">Delete</button>
                        </form>
                    </div>
                @else
                    <p class="mt-6 text-sm font-medium text-stone-400">Read only</p>
                @endif
            </article>
        @empty
            <article class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 xl:col-span-2">
                No service listings yet.
            </article>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $services->links() }}
    </div>
</x-cms.layouts.app>