<x-cms.layouts.app title="Edit Content" eyebrow="CMS Content" heading="Edit content" subheading="Update publication state, audience, and rich text body content before it reaches the public website and mobile app.">
    <form method="POST" action="{{ route('cms.contents.update', $content) }}">
        @csrf
        @method('PUT')
        @include('cms.contents._form', ['submitLabel' => 'Update content'])
    </form>

    <section class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-6">
        <h3 class="text-lg font-semibold text-white">Block readiness</h3>
        <p class="mt-2 text-sm text-stone-400">The main body now uses CKEditor. This entry currently has {{ $content->blocks->count() }} block{{ $content->blocks->count() === 1 ? '' : 's' }} stored separately for a future block-management workflow.</p>
    </section>
</x-cms.layouts.app>