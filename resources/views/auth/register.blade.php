<x-layouts.site title="Register | SRHR Connect">
    <section class="mx-auto grid min-h-[calc(100vh-88px)] max-w-7xl items-center gap-8 px-5 py-10 sm:px-8 lg:grid-cols-[1.05fr_0.95fr] lg:px-10">
        <div class="rounded-[2rem] border border-white/10 bg-gradient-to-br from-white/10 to-white/5 p-8 backdrop-blur">
            <p class="text-xs uppercase tracking-[0.35em] text-orange-200">Create Access</p>
            <h1 class="mt-3 text-4xl font-semibold text-white" style="font-family: 'Figtree', sans-serif;">Create your SRHR Connect account.</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-stone-300">This registration flow supports person-space accounts for mobile and personalized features. CMS access is reserved for users who have been granted administrator permissions, while public SRHR pages stay open to everyone.</p>

            <div class="mt-8 space-y-4">
                <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-4">
                    <p class="text-sm font-semibold text-white">What you get</p>
                    <p class="mt-2 text-sm leading-6 text-stone-400">An authenticated account that works with the mobile API, permission syncing, and future personalized experiences.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-stone-950/40 p-4">
                    <p class="text-sm font-semibold text-white">What comes next</p>
                    <p class="mt-2 text-sm leading-6 text-stone-400">Administrators can grant CMS access separately, while the same identity remains valid for public browsing and mobile flows.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-stone-950/60 p-8 shadow-2xl shadow-black/30">
            <h2 class="text-2xl font-semibold text-white">Create account</h2>
            <p class="mt-2 text-sm text-stone-400">Use a real email so seeded and authored content can be attributed correctly.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="name" class="text-sm font-medium text-stone-200">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition focus:border-emerald-400" required autofocus>
                    @error('name')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-medium text-stone-200">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition focus:border-emerald-400" required>
                    @error('email')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-stone-200">Password</label>
                    <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition focus:border-emerald-400" required>
                    @error('password')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-sm font-medium text-stone-200">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition focus:border-emerald-400" required>
                </div>

                <button type="submit" class="w-full rounded-full bg-emerald-400 px-5 py-3.5 font-semibold text-stone-950 transition hover:bg-emerald-300">Create account</button>
            </form>

            <p class="mt-6 text-sm text-stone-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">Sign in</a>
            </p>

            <p class="mt-3 text-sm text-stone-500">
                Prefer to explore first?
                <a href="{{ route('public.contents.index') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">Read public content</a>
            </p>
        </div>
    </section>
</x-layouts.site>