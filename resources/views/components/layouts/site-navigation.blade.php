@php
    $items = collect($items ?? []);
    $level = $level ?? 0;
@endphp

@if ($items->isNotEmpty())
    <ul class="{{ $level === 0 ? 'flex flex-col gap-2 lg:flex-row lg:flex-wrap lg:items-center lg:justify-end' : 'space-y-2' }}">
        @foreach ($items as $item)
            @php
                $children = collect($item['children'] ?? []);
                $href = $item['href'] ?? null;
                $topLevelClasses = 'flex items-center justify-between gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2.5 font-medium text-white transition hover:border-white/25 hover:bg-white/15';
                $nestedClasses = 'block rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm font-medium text-white transition hover:border-emerald-300/40 hover:text-emerald-200';
            @endphp

            <li class="{{ $level === 0 ? 'relative' : '' }}">
                @if ($children->isEmpty())
                    <a href="{{ $href }}" class="{{ $level === 0 ? $topLevelClasses : $nestedClasses }}">{{ $item['title'] }}</a>
                @else
                    <details class="site-nav-details {{ $level === 0 ? 'lg:min-w-[11rem]' : '' }}">
                        <summary class="{{ $level === 0 ? $topLevelClasses : $nestedClasses.' flex cursor-pointer items-center justify-between gap-3' }} cursor-pointer list-none">
                            <span>{{ $item['title'] }}</span>
                            <svg class="site-nav-chevron h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0l-4.25-4.51a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </summary>

                        <div class="site-nav-panel mt-2 rounded-3xl border border-white/10 bg-stone-950/90 p-2 shadow-xl shadow-stone-950/10 lg:absolute lg:right-0 lg:mt-3 lg:min-w-72">
                            @if (filled($href))
                                <a href="{{ $href }}" class="mb-2 block rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-4 py-3 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-300/15">
                                    Open {{ $item['title'] }}
                                </a>
                            @endif

                            @include('components.layouts.site-navigation', ['items' => $children, 'level' => $level + 1])
                        </div>
                    </details>
                @endif
            </li>
        @endforeach
    </ul>
@endif