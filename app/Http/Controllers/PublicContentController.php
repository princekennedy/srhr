<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Faq;
use App\Models\Quiz;
use App\Models\ServiceCenter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    public function categories(): View
    {
        $categories = ContentCategory::query()
            ->where('is_active', true)
            ->withCount([
                'contents' => fn ($query) => $query
                    ->where('status', 'published')
                    ->where('visibility', 'public'),
            ])
            ->with([
                'contents' => fn ($query) => $query
                    ->where('status', 'published')
                    ->where('visibility', 'public')
                    ->latest('published_at')
                    ->limit(3),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function showCategory(ContentCategory $category): View
    {
        abort_unless($category->is_active, 404);

        $contents = $category->contents()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.categories.show', [
            'category' => $category,
            'contents' => $contents,
        ]);
    }

    public function contents(Request $request): View
    {
        $selectedCategory = $request->string('category')->toString();
        $selectedType = $request->string('type')->toString();
        $search = trim($request->string('q')->toString());

        $contents = Content::query()
            ->with('category')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->when($selectedCategory !== '', function ($query) use ($selectedCategory): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $selectedCategory));
            })
            ->when($selectedType !== '', fn ($query) => $query->where('content_type', $selectedType))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.contents.index', [
            'contents' => $contents,
            'categories' => ContentCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'typeOptions' => Content::TYPE_OPTIONS,
            'selectedCategory' => $selectedCategory,
            'selectedType' => $selectedType,
            'search' => $search,
        ]);
    }

    public function showContent(Content $content): View
    {
        abort_unless($content->status === 'published' && $content->visibility === 'public', 404);

        $content->load([
            'category',
            'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
        ]);

        $relatedContents = Content::query()
            ->with('category')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->whereKeyNot($content->getKey())
            ->when($content->category_id !== null, fn ($query) => $query->where('category_id', $content->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.contents.show', [
            'content' => $content,
            'relatedContents' => $relatedContents,
        ]);
    }

    public function faqs(Request $request): View
    {
        $selectedCategory = $request->string('category')->toString();

        $faqs = Faq::query()
            ->with('category')
            ->where('is_published', true)
            ->where('visibility', 'public')
            ->when($selectedCategory !== '', function ($query) use ($selectedCategory): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $selectedCategory));
            })
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        return view('public.faqs.index', [
            'faqs' => $faqs,
            'categories' => ContentCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function quizzes(): View
    {
        $quizzes = Quiz::query()
            ->withCount('questions')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->latest('published_at')
            ->paginate(9);

        return view('public.quizzes.index', [
            'quizzes' => $quizzes,
        ]);
    }

    public function showQuiz(Quiz $quiz): View
    {
        abort_unless($quiz->status === 'published' && $quiz->visibility === 'public', 404);

        $quiz->load([
            'questions' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            'questions.options' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return view('public.quizzes.show', [
            'quiz' => $quiz,
        ]);
    }

    public function services(Request $request): View
    {
        $selectedDistrict = $request->string('district')->toString();

        $services = ServiceCenter::query()
            ->with('category')
            ->where('is_active', true)
            ->where('visibility', 'public')
            ->when($selectedDistrict !== '', fn ($query) => $query->where('district', $selectedDistrict))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('public.services.index', [
            'services' => $services,
            'districts' => ServiceCenter::query()
                ->where('is_active', true)
                ->where('visibility', 'public')
                ->whereNotNull('district')
                ->orderBy('district')
                ->distinct()
                ->pluck('district'),
            'selectedDistrict' => $selectedDistrict,
        ]);
    }

    public function showService(ServiceCenter $service): View
    {
        abort_unless($service->is_active && $service->visibility === 'public', 404);

        $service->load('category');

        $relatedServices = ServiceCenter::query()
            ->where('is_active', true)
            ->where('visibility', 'public')
            ->whereKeyNot($service->getKey())
            ->when($service->district !== null, fn ($query) => $query->where('district', $service->district))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->limit(3)
            ->get();

        return view('public.services.show', [
            'service' => $service,
            'relatedServices' => $relatedServices,
        ]);
    }
}