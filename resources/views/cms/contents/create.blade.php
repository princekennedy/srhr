<x-cms.layouts.app title="New Content" eyebrow="CMS Content" heading="Create content" subheading="Create a publishable content entry with rich text formatting for the public website and mobile experience.">
    <form method="POST" action="{{ route('cms.contents.store') }}">
        @csrf
        @include('cms.contents._form', ['submitLabel' => 'Create content'])
    </form>
</x-cms.layouts.app>