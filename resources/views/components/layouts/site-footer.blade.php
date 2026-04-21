<footer class="border-t border-slate-200 bg-white/90 transition-colors duration-200 dark:border-slate-800 dark:bg-slate-950/90">
  <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.8fr_0.8fr]">
      <div>
        <a href="/" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Brandly</a>
        <p class="mt-4 max-w-md text-sm leading-7 text-slate-600 dark:text-slate-400">
          Clean landing pages, polished product storytelling, and modern UI patterns that stay consistent from first visit to sign-in.
        </p>
      </div>

      <div>
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Explore</h2>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          <a href="/#home" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
          <a href="/#features" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Features</a>
          <a href="/#contact" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Contact</a>
        </div>
      </div>

      <div>
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Services</h2>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          <a href="/#web" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Web Development</a>
          <a href="/#mobile" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Mobile Apps</a>
          <a href="/#cloud" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Cloud Solutions</a>
        </div>
      </div>

      <div>
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Account</h2>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          <a href="/login" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Login</a>
          <a href="/register" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Get Started</a>
        </div>
      </div>
    </div>

    <div class="mt-10 flex flex-col gap-3 border-t border-slate-200 pt-6 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
      <p>&copy; {{ now()->year }} Brandly. All rights reserved.</p>
      <p>Built with the same visual pattern as the landing experience.</p>
    </div>
  </div>
</footer>