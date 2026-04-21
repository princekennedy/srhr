<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\ServiceCenterRequest;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\ServiceCenter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceCenterController extends Controller
{
    public function index(): View
    {
        return view('cms.services.index', [
            'services' => ServiceCenter::query()
                ->with('category')
                ->latest('updated_at')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('cms.services.create', [
            'service' => new ServiceCenter(),
            'categories' => ContentCategory::query()->orderBy('name')->get(),
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function store(ServiceCenterRequest $request): RedirectResponse
    {
        ServiceCenter::create([
            ...$request->validated(),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('cms.services.index')
            ->with('status', 'Service directory entry created.');
    }

    public function edit(ServiceCenter $service): View
    {
        return view('cms.services.edit', [
            'service' => $service,
            'categories' => ContentCategory::query()->orderBy('name')->get(),
            'audienceOptions' => Content::AUDIENCE_OPTIONS,
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function update(ServiceCenterRequest $request, ServiceCenter $service): RedirectResponse
    {
        $service->update([
            ...$request->validated(),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('cms.services.index')
            ->with('status', 'Service directory entry updated.');
    }

    public function destroy(ServiceCenter $service): RedirectResponse
    {
        $service->delete();

        return redirect()
            ->route('cms.services.index')
            ->with('status', 'Service directory entry deleted.');
    }
}