<x-layouts.site title="Log In | SRHR Connect">
    <section class="mx-auto grid min-h-[calc(100vh-88px)] max-w-7xl items-center gap-8 px-5 py-10 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:px-10">
        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Welcome Back</p>
            <h1 class="mt-3 text-4xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Sign in to manage SRHR content.</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-stone-300">Use your account to access the CMS workspace if you have administrator permissions, or to manage your personal platform identity while public SRHR pages remain available without sign-in.</p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-4">
                    <p class="text-sm font-semibold text-white">Secure session auth</p>
                    <p class="mt-2 text-sm leading-6 text-stone-400">Laravel session authentication protects the CMS, while role-aware checks prevent non-admin users from entering administrator screens.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-4">
                    <p class="text-sm font-semibold text-white">Public pages stay open</p>
                    <p class="mt-2 text-sm leading-6 text-stone-400">Visitors can still read published content, FAQs, quizzes, and service listings without logging in.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-stone-950/60 p-8 shadow-2xl shadow-black/30">
            <h2 class="text-2xl font-semibold text-white">Log in</h2>
            <p class="mt-2 text-sm text-stone-400">Enter your email and password to continue to the CMS dashboard.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-stone-200">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition focus:border-emerald-400" required autofocus>
                    @error('email')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-stone-200">Password</label>
                    <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition focus:border-emerald-400" required>
                </div>

                <label class="flex items-center gap-3 text-sm text-stone-300">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/20 bg-transparent text-emerald-400 focus:ring-emerald-400">
                    Keep me signed in on this device
                </label>

                <button type="submit" class="w-full rounded-full bg-emerald-400 px-5 py-3.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Continue to CMS</button>
            </form>

            <p class="mt-6 text-sm text-stone-400">
                Need an account?
                <a href="{{ route('register') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">Create one here</a>
            </p>

            <p class="mt-3 text-sm text-stone-500">
                Looking for public information instead?
                <a href="{{ route('public.contents.index') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">Browse content</a>
            </p>
        </div>
    </section>
</x-layouts.site>