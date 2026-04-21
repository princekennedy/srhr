<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\ContentRequest;
use App\Models\Content;
use App\Models\ContentCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class ContentController extends Controller
{
    public function index(): View
    {
        return view('cms.contents.index', [
            'contents' => Content::query()
                ->with(['category', 'blocks'])
                ->latest('updated_at')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('cms.contents.create', [
            'content' => new Content(),
            'categories' => ContentCategory::query()->orderBy('name')->get(),
            'typeOptions' => Content::TYPE_OPTIONS,
            'statusOptions' => Content::STATUS_OPTIONS,
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function store(ContentRequest $request): RedirectResponse
    {
        Content::create($this->validatedPayload($request));

        return redirect()
            ->route('cms.contents.index')
            ->with('status', 'Content entry created.');
    }

    public function edit(Content $content): View
    {
        $content->load('blocks');

        return view('cms.contents.edit', [
            'content' => $content,
            'categories' => ContentCategory::query()->orderBy('name')->get(),
            'typeOptions' => Content::TYPE_OPTIONS,
            'statusOptions' => Content::STATUS_OPTIONS,
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function update(ContentRequest $request, Content $content): RedirectResponse
    {
        $content->update($this->validatedPayload($request, $content));

        return redirect()
            ->route('cms.contents.index')
            ->with('status', 'Content entry updated.');
    }

    public function destroy(Content $content): RedirectResponse
    {
        $content->delete();

        return redirect()
            ->route('cms.contents.index')
            ->with('status', 'Content entry deleted.');
    }

    private function validatedPayload(ContentRequest $request, ?Content $content = null): array
    {
        $payload = $request->validated();
        $userId = $request->user()?->id;

        if (($payload['status'] ?? null) === 'published' && blank($payload['published_at'] ?? null)) {
            $payload['published_at'] = Carbon::now();
        }

        $payload['created_by'] = $content?->created_by ?? $userId;
        $payload['updated_by'] = $userId;

        return $payload;
    }
}