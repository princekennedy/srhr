<x-layouts.app :title="$isCategoryMode ? 'New Category' : 'New Content'" eyebrow="CMS Content" :heading="$isCategoryMode ? 'Create category' : 'Create content'" :subheading="$isCategoryMode ? 'Create a root category record that groups and previews its child content entries.' : 'Create a publishable content entry with rich text formatting for the public website and mobile experience.'">
    <div class="mb-6 flex gap-3">
        <a href="{{ request('category_id') ? route('cms.contents.edit', ['content' => request('category_id'), 'tab' => 'children']) : route('cms.contents.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-stone-400 dark:hover:text-stone-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back
        </a>
    </div>
    <form method="POST" action="{{ route('cms.contents.store', $isCategoryMode ? ['kind' => 'category'] : []) }}" enctype="multipart/form-data">
        @csrf
        @if ($isCategoryMode)
            @include('cms.contents._category-form', ['submitLabel' => 'Create category'])
        @else
            @include('cms.contents._form', ['submitLabel' => 'Create content'])
        @endif
    </form>
</x-layouts.app>