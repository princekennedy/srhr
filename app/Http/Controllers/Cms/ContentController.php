<?php

namespace App\Http\Controllers\Cms;

use App\Enums\CategoryLayoutType;
use App\Enums\ContentLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\ContentRequest;
use App\Models\Content;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class ContentController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        abort_if(
            $user !== null
            && ! $user->hasCmsPermission('cms.manage.contents')
            && ! $user->hasCmsPermission('cms.manage.categories'),
            403,
        );

        return view('cms.contents.index', [
            'categories' => Content::query()
                ->categories()
                ->withCount('contents')
                ->with(['contents.blocks'])
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        $this->authorizeContentMode(request()->user(), $this->isCategoryMode());

        return view('cms.contents.create', [
            'content' => new Content(),
            'category' => new Content(),
            'categories' => Content::query()->categories()->orderBy('title')->get(),
            'menuItemOptions' => Menu::query()->items()->orderBy('title')->get(),
            'typeOptions' => Content::TYPE_OPTIONS,
            'statusOptions' => Content::STATUS_OPTIONS,
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
            'layoutOptions' => $this->isCategoryMode() ? CategoryLayoutType::options() : ContentLayoutType::options(),
            'isCategoryMode' => $this->isCategoryMode(),
        ]);
    }

    public function store(ContentRequest $request): RedirectResponse
    {
        $this->authorizeContentMode($request->user(), $request->isCategoryRequest());

        $content = Content::create($this->validatedPayload($request));

        $this->syncMedia($request, $content);

        return redirect()
            ->route('cms.contents.index')
            ->with('status', $request->isCategoryRequest() ? 'Category created.' : 'Content entry created.');
    }

    public function edit(Content $content): View
    {
        $this->authorizeContentMode(request()->user(), $content->isCategory());

        $content->load($content->isCategory() ? ['contents.blocks', 'menuItem'] : ['blocks', 'category']);

        return view('cms.contents.edit', [
            'content' => $content,
            'categories' => Content::query()->categories()->orderBy('title')->get(),
            'menuItemOptions' => Menu::query()->items()->orderBy('title')->get(),
            'typeOptions' => Content::TYPE_OPTIONS,
            'statusOptions' => Content::STATUS_OPTIONS,
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
            'layoutOptions' => $content->isCategory() ? CategoryLayoutType::options() : ContentLayoutType::options(),
        ]);
    }

    public function update(ContentRequest $request, Content $content): RedirectResponse
    {
        $this->authorizeContentMode($request->user(), $content->isCategory());

        $content->update($this->validatedPayload($request, $content));

        $this->syncMedia($request, $content);

        return redirect()
            ->route('cms.contents.index')
            ->with('status', $content->isCategory() ? 'Category updated.' : 'Content entry updated.');
    }

    public function destroy(Content $content): RedirectResponse
    {
        $this->authorizeContentMode(request()->user(), $content->isCategory());

        $message = $content->isCategory() ? 'Category deleted.' : 'Content entry deleted.';

        $content->delete();

        return redirect()
            ->route('cms.contents.index')
            ->with('status', $message);
    }

    private function validatedPayload(ContentRequest $request, ?Content $content = null): array
    {
        $payload = $request->validated();
        $userId = $request->user()?->id;

        if ($request->isCategoryRequest() || $content?->isCategory()) {
            $payload['content_type'] = 'category';
            $payload['status'] = 'published';
            $payload['audience'] = $content?->audience ?? 'general';
            $payload['published_at'] = $content?->published_at ?? Carbon::now();
            $payload['parent_id'] = null;
            $payload['sort_order'] = $request->integer('sort_order');
            $payload['is_active'] = $request->boolean('is_active');
            $payload['created_by'] = $content?->created_by ?? $userId;
            $payload['updated_by'] = $userId;

            return $payload;
        }

        unset($payload['featured_image_upload'], $payload['attachments']);

        if (($payload['status'] ?? null) === 'published' && blank($payload['published_at'] ?? null)) {
            $payload['published_at'] = Carbon::now();
        }

        $payload['parent_id'] = $request->integer('category_id');
        $payload['sort_order'] = $request->integer('sort_order');
        $payload['is_active'] = $request->boolean('is_active');

        $payload['created_by'] = $content?->created_by ?? $userId;
        $payload['updated_by'] = $userId;

        unset($payload['category_id']);

        return $payload;
    }

    private function syncMedia(ContentRequest $request, Content $content): void
    {
        if ($request->isCategoryRequest() || $content->isCategory()) {
            return;
        }

        if ($request->hasFile('featured_image_upload')) {
            $content
                ->addMediaFromRequest('featured_image_upload')
                ->toMediaCollection('featured_image');

            $content->forceFill([
                'featured_image_path' => null,
            ])->save();
        }

        foreach ($request->file('attachments', []) as $attachment) {
            $content
                ->addMedia($attachment)
                ->toMediaCollection('attachments');
        }
    }

    private function isCategoryMode(): bool
    {
        return request()->string('kind')->lower()->value() === 'category';
    }

    private function authorizeContentMode($user, bool $isCategory): void
    {
        if ($user === null) {
            abort(403);
        }

        $permission = $isCategory ? 'cms.manage.categories' : 'cms.manage.contents';

        abort_if(! $user->hasCmsPermission($permission), 403);
    }
}