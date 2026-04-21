<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\ContentCategoryRequest;
use App\Models\ContentCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContentCategoryController extends Controller
{
    public function index(): View
    {
        return view('cms.categories.index', [
            'categories' => ContentCategory::query()
                ->withCount('contents')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('cms.categories.create', [
            'category' => new ContentCategory(),
        ]);
    }

    public function store(ContentCategoryRequest $request): RedirectResponse
    {
        ContentCategory::create([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.categories.index')
            ->with('status', 'Category created.');
    }

    public function edit(ContentCategory $category): View
    {
        return view('cms.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(ContentCategoryRequest $request, ContentCategory $category): RedirectResponse
    {
        $category->update([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.categories.index')
            ->with('status', 'Category updated.');
    }

    public function destroy(ContentCategory $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('cms.categories.index')
            ->with('status', 'Category deleted.');
    }
}