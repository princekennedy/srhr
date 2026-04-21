<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\FaqRequest;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Faq;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('cms.faqs.index', [
            'faqs' => Faq::query()
                ->with('category')
                ->orderBy('sort_order')
                ->latest('updated_at')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('cms.faqs.create', [
            'faq' => new Faq(),
            'categories' => ContentCategory::query()->orderBy('name')->get(),
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::create([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'is_published' => $request->boolean('is_published'),
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('cms.faqs.index')
            ->with('status', 'FAQ entry created.');
    }

    public function edit(Faq $faq): View
    {
        return view('cms.faqs.edit', [
            'faq' => $faq,
            'categories' => ContentCategory::query()->orderBy('name')->get(),
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'is_published' => $request->boolean('is_published'),
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('cms.faqs.index')
            ->with('status', 'FAQ entry updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('cms.faqs.index')
            ->with('status', 'FAQ entry deleted.');
    }
}