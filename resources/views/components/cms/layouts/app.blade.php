<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'CMS' }} | SRHR</title>
        <script>
            (() => {
                const storedTheme = window.localStorage.getItem('srhr-cms-theme') || 'light';
                document.documentElement.classList.toggle('dark', storedTheme === 'dark');
            })();
        </script>
        @unless (app()->runningUnitTests())
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 transition-colors dark:bg-stone-950 dark:text-stone-100">
        <div class="grid min-h-screen lg:grid-cols-[280px_1fr]">
            <aside class="border-b border-slate-200 bg-white/90 px-6 py-8 backdrop-blur lg:border-b-0 lg:border-r dark:border-white/10 dark:bg-stone-900/90">
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-600 dark:text-emerald-300">SRHR Platform</p>
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">CMS Workspace</h1>
                    <p class="text-sm leading-6 text-slate-600 dark:text-stone-400">Manage youth-friendly content, menus, settings, and app-facing publishing workflows from a single dashboard.</p>
                </div>

                <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500 dark:text-stone-400">Signed in as</p>
                    <p class="mt-2 text-base font-semibold text-slate-900 dark:text-white">{{ auth()->user()?->name }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach (auth()->user()?->getRoleNames() ?? [] as $role)
                            <span class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:border-white/10 dark:text-emerald-200">{{ $role }}</span>
                        @endforeach
                    </div>
                    <p class="mt-3 text-sm text-slate-600 dark:text-stone-400">
                        {{ auth()->user()?->canManageAnyCmsModule() ? 'Management actions are enabled for this account.' : 'This account has read-only dashboard access.' }}
                    </p>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="button" data-theme-toggle class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-stone-200 dark:hover:bg-white/10">
                        Toggle theme
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-stone-400 dark:hover:text-white">Sign out</button>
                    </form>
                </div>

                <nav class="mt-8 space-y-2">
                    @php
                        $navigation = [
                            ['label' => 'Dashboard', 'route' => 'cms.dashboard'],
                            ['label' => 'Categories', 'route' => 'cms.categories.index'],
                            ['label' => 'Content', 'route' => 'cms.contents.index'],
                            ['label' => 'FAQs', 'route' => 'cms.faqs.index'],
                            ['label' => 'Quizzes', 'route' => 'cms.quizzes.index'],
                            ['label' => 'Services', 'route' => 'cms.services.index'],
                            ['label' => 'Menus', 'route' => 'cms.menus.index'],
                            ['label' => 'Settings', 'route' => 'cms.settings.index'],
                        ];
                    @endphp

                    @foreach ($navigation as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center justify-between rounded-2xl border px-4 py-3 text-sm transition {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'border-emerald-400/40 bg-emerald-50 text-slate-900 dark:border-emerald-400/50 dark:bg-emerald-400/10 dark:text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:border-white/20 dark:hover:bg-white/10 dark:hover:text-white' }}"
                        >
                            <span>{{ $item['label'] }}</span>
                            <span class="text-xs uppercase tracking-[0.3em] text-slate-400 dark:text-stone-500">Open</span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            <main class="cms-surface px-5 py-6 sm:px-8 lg:px-10">
                <div class="mx-auto max-w-7xl">
                    <header class="mb-8 flex flex-col gap-4 border-b border-slate-200 pb-6 md:flex-row md:items-end md:justify-between dark:border-white/10">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-600 dark:text-emerald-300">{{ $eyebrow ?? 'Content Management System' }}</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ $heading ?? 'CMS' }}</h2>
                            @if (! empty($subheading ?? null))
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-stone-400">{{ $subheading }}</p>
                            @endif
                        </div>

                        @isset($headerAction)
                            <div>
                                {{ $headerAction }}
                            </div>
                        @endisset
                    </header>

                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-100">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-400/30 dark:bg-rose-400/10 dark:text-rose-100">
                            <p class="font-semibold">Please correct the highlighted fields.</p>
                            <ul class="mt-2 space-y-1 text-rose-700/90 dark:text-rose-50/90">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        <script>
            (() => {
                const toggle = document.querySelector('[data-theme-toggle]');
                if (!toggle) {
                    return;
                }

                toggle.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    window.localStorage.setItem('srhr-cms-theme', isDark ? 'dark' : 'light');
                });
            })();
        </script>
    </body>
</html>