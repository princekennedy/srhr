<x-cms.layouts.app title="Settings" eyebrow="CMS Runtime" heading="App settings" subheading="Manage public app labels, support contacts, and operational configuration values used at runtime.">
    @if (! auth()->user()?->hasCmsPermission('cms.manage.settings'))
        <div class="mb-6 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-stone-400">
            This account can review settings but cannot update them.
        </div>
    @endif

    <form method="POST" action="{{ route('cms.settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach ($settings as $group => $groupSettings)
            <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="mb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">{{ $group }}</p>
                    <h3 class="mt-2 text-xl font-semibold text-white">{{ str($group)->headline() }}</h3>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    @foreach ($groupSettings as $setting)
                        <div class="rounded-2xl border border-white/10 bg-stone-950/40 p-4">
                            <label for="setting_{{ $setting->key }}" class="text-sm font-medium text-stone-200">{{ $setting->label }}</label>
                            @if ($setting->input_type === 'textarea')
                                <textarea id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                            @elseif ($setting->input_type === 'boolean')
                                <label class="mt-3 flex items-center gap-3 text-sm text-stone-200">
                                    <input id="setting_{{ $setting->key }}" type="checkbox" name="settings[{{ $setting->key }}]" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('settings.'.$setting->key, $setting->value) == '1')>
                                    Enabled
                                </label>
                            @else
                                <input id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" type="text" value="{{ old('settings.'.$setting->key, $setting->value) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                            @endif

                            @if ($setting->description)
                                <p class="mt-2 text-xs leading-5 text-stone-500">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="flex justify-end">
            @if (auth()->user()?->hasCmsPermission('cms.manage.settings'))
                <button type="submit" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">Save settings</button>
            @endif
        </div>
    </form>
</x-cms.layouts.app>