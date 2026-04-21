<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'SRHR Connect' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|figtree:500,600,700,800" rel="stylesheet" />
        <script>
            (() => {
                const storedTheme = window.localStorage.getItem('srhr-site-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const activeTheme = storedTheme ?? (prefersDark ? 'dark' : 'light');

                document.documentElement.classList.toggle('dark', activeTheme === 'dark');
            })();
        </script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen text-stone-100" style="font-family: 'Instrument Sans', sans-serif;">
        @php
            $siteNavigation = collect($siteNavigation ?? []);
        @endphp

        <div class="site-shell min-h-screen">
            <header class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-5 sm:px-8 lg:flex-row lg:items-start lg:justify-between lg:px-10">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-lg font-bold text-emerald-200">S</span>
                    <span>
                        <span class="block text-xs uppercase tracking-[0.35em] text-emerald-300">SRHR Platform</span>
                        <span class="block text-lg font-semibold text-white">SRHR Connect</span>
                    </span>
                </a>

                <div class="flex w-full flex-col gap-3 lg:w-auto lg:items-end">
                    <div class="flex items-center justify-end lg:hidden">
                        <button type="button" data-site-nav-toggle aria-expanded="false" class="rounded-full border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-medium text-white transition hover:border-white/25 hover:bg-white/15">
                            Menu
                        </button>
                    </div>

                    <div data-site-nav-panel class="hidden w-full flex-col gap-3 lg:flex lg:w-auto lg:items-end">
                        <nav class="text-sm" aria-label="Public site navigation">
                            @include('components.layouts.site-navigation', ['items' => $siteNavigation, 'level' => 0])
                        </nav>

                        <div class="flex flex-wrap items-center gap-3 text-sm lg:justify-end">
                            <button type="button" data-site-theme-toggle class="rounded-full border border-white/15 bg-white/10 px-4 py-2.5 font-medium text-white transition hover:border-white/25 hover:bg-white/15">Toggle theme</button>
                        @auth
                            @if (auth()->user()?->canAccessCms())
                                <a href="{{ route('cms.dashboard') }}" class="rounded-full border border-white/15 bg-white/10 px-4 py-2.5 font-medium text-white transition hover:border-white/25 hover:bg-white/15">CMS</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full bg-emerald-400 px-4 py-2.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Log out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-white/15 bg-white/10 px-4 py-2.5 font-medium text-white transition hover:border-white/25 hover:bg-white/15">Log in</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-emerald-400 px-4 py-2.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Create account</a>
                        @endauth
                        </div>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="mx-auto max-w-7xl px-5 pb-2 sm:px-8 lg:px-10">
                    <div class="rounded-2xl border border-emerald-400/25 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            (() => {
                const toggle = document.querySelector('[data-site-theme-toggle]');
                const navToggle = document.querySelector('[data-site-nav-toggle]');
                const navPanel = document.querySelector('[data-site-nav-panel]');
                const dropdowns = Array.from(document.querySelectorAll('.site-nav-details'));

                if (!toggle) {
                    return;
                }

                const syncLabel = () => {
                    toggle.textContent = document.documentElement.classList.contains('dark') ? 'Light mode' : 'Dark mode';
                };

                syncLabel();

                toggle.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    window.localStorage.setItem('srhr-site-theme', isDark ? 'dark' : 'light');
                    syncLabel();
                });

                if (navToggle && navPanel) {
                    navToggle.addEventListener('click', () => {
                        navPanel.classList.toggle('hidden');
                        navToggle.setAttribute('aria-expanded', navPanel.classList.contains('hidden') ? 'false' : 'true');
                    });
                }

                dropdowns.forEach((dropdown) => {
                    const summary = dropdown.querySelector('summary');

                    if (!summary) {
                        return;
                    }

                    summary.addEventListener('click', () => {
                        dropdowns.forEach((otherDropdown) => {
                            if (otherDropdown !== dropdown && !otherDropdown.contains(dropdown)) {
                                otherDropdown.removeAttribute('open');
                            }
                        });
                    });
                });

                document.addEventListener('click', (event) => {
                    dropdowns.forEach((dropdown) => {
                        if (!dropdown.contains(event.target)) {
                            dropdown.removeAttribute('open');
                        }
                    });
                });
            })();
        </script>
    </body>
</html>