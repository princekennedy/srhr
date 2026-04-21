<x-layouts.site :title="$service->name.' | Services | SRHR Connect'">
    <section class="mx-auto max-w-6xl px-5 pb-12 pt-8 sm:px-8 lg:px-10">
        <a href="{{ route('public.services.index') }}" class="text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">Back to services</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <article class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                    <span>{{ $service->district ?: 'District pending' }}</span>
                    <span>{{ $service->category?->name ?? 'General' }}</span>
                </div>
                <h1 class="mt-4 text-5xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">{{ $service->name }}</h1>
                @if ($service->summary)
                    <p class="mt-4 text-lg leading-8 text-stone-300">{{ $service->summary }}</p>
                @endif

                <div class="mt-8 grid gap-5 md:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-5">
                        <p class="text-xs uppercase tracking-[0.3em] text-orange-200">Location</p>
                        <p class="mt-3 text-base leading-7 text-stone-300">{{ $service->physical_address ?: 'Address not provided yet.' }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-5">
                        <p class="text-xs uppercase tracking-[0.3em] text-orange-200">Availability</p>
                        <p class="mt-3 text-base leading-7 text-stone-300">{{ $service->service_hours ?: 'Service hours not provided yet.' }}</p>
                    </div>
                </div>

                @if ($service->services)
                    <section class="mt-6 rounded-3xl border border-white/10 bg-stone-950/40 p-5">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Services Offered</p>
                        <p class="mt-3 whitespace-pre-line text-base leading-8 text-stone-300">{{ $service->services }}</p>
                    </section>
                @endif
            </article>

            <aside class="space-y-5">
                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Contact</p>
                    <div class="mt-4 space-y-3 text-sm text-stone-300">
                        <p><span class="font-semibold text-white">Phone:</span> {{ $service->contact_phone ?: 'Not listed' }}</p>
                        <p><span class="font-semibold text-white">Email:</span> {{ $service->contact_email ?: 'Not listed' }}</p>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <p class="text-xs uppercase tracking-[0.3em] text-orange-200">Nearby or Related</p>
                    <div class="mt-4 space-y-3">
                        @forelse ($relatedServices as $relatedService)
                            <a href="{{ route('public.services.show', $relatedService) }}" class="block rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 transition hover:border-emerald-300/40 hover:bg-stone-950/60">
                                <p class="font-semibold text-white">{{ $relatedService->name }}</p>
                                <p class="mt-1 text-sm text-stone-400">{{ $relatedService->district ?: 'District pending' }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-stone-500">No related services are available yet.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </section>
</x-layouts.site>