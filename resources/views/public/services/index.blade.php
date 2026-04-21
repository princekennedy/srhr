<x-layouts.site title="Services | SRHR Connect">
    <section class="mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <div class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Public Services</p>
            <h1 class="mt-3 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Find youth-friendly services and referral points.</h1>
            <p class="mt-4 text-lg leading-8 text-stone-300">These public listings are visible to visitors and mobile users without login.</p>
        </div>

        <form method="GET" action="{{ route('public.services.index') }}" class="mt-8 flex flex-wrap items-end gap-4 rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="min-w-[260px] flex-1">
                <label for="district" class="text-sm font-medium text-stone-200">District</label>
                <select id="district" name="district" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                    <option value="">All districts</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district }}" @selected($selectedDistrict === $district)>{{ $district }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="rounded-full bg-emerald-400 px-5 py-3 font-semibold text-stone-950 transition hover:bg-emerald-300">Filter</button>
                <a href="{{ route('public.services.index') }}" class="rounded-full border border-white/15 bg-white/10 px-5 py-3 font-semibold text-white transition hover:border-white/25 hover:bg-white/15">Reset</a>
            </div>
        </form>

        <div class="mt-8 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
            @forelse ($services as $service)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                        <span>{{ $service->district ?: 'District pending' }}</span>
                        @if ($service->is_featured)
                            <span class="text-emerald-300">Featured</span>
                        @endif
                    </div>
                    <h2 class="mt-3 text-2xl font-semibold text-white">{{ $service->name }}</h2>
                    <p class="mt-3 text-sm leading-6 text-stone-400">{{ $service->summary ?: 'Public referral and service directory entry.' }}</p>
                    <div class="mt-4 space-y-1 text-sm text-stone-300">
                        @if ($service->service_hours)
                            <p>{{ $service->service_hours }}</p>
                        @endif
                        @if ($service->contact_phone)
                            <p>{{ $service->contact_phone }}</p>
                        @endif
                    </div>
                    <a href="{{ route('public.services.show', $service) }}" class="mt-5 inline-flex text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">View service</a>
                </article>
            @empty
                <article class="rounded-[2rem] border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 lg:col-span-2 xl:col-span-3">
                    No public services matched the current filters.
                </article>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $services->links() }}
        </div>
    </section>
</x-layouts.site>