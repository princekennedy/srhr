<?php

namespace App\Support;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicNavigation
{
    public function items(string $location = 'public-primary'): Collection
    {
        $menu = $this->menu($location);

        if (! $menu) {
            return $this->fallbackItems();
        }

        $items = $menu->items
            ->whereNull('parent_id')
            ->sortBy('sort_order')
            ->values()
            ->map(fn (MenuItem $item): array => $this->mapItem($item, $menu->items))
            ->filter(fn (array $item): bool => filled($item['href']) || collect($item['children'])->isNotEmpty())
            ->values();

        return $items->isNotEmpty() ? $items : $this->fallbackItems();
    }

    public function menu(string $location = 'public-primary'): ?Menu
    {
        if (! Schema::hasTable('menus') || ! Schema::hasTable('menu_items')) {
            return null;
        }

        return Menu::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->with([
                'items' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('visibility', 'public')
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->first();
    }

    private function mapItem(MenuItem $item, Collection $allItems): array
    {
        $children = $allItems
            ->where('parent_id', $item->id)
            ->sortBy('sort_order')
            ->values()
            ->map(fn (MenuItem $child): array => $this->mapItem($child, $allItems))
            ->filter(fn (array $child): bool => filled($child['href']) || collect($child['children'])->isNotEmpty())
            ->values();

        return [
            'title' => $item->title,
            'href' => $this->resolveMenuItemUrl($item),
            'children' => $children,
        ];
    }

    private function fallbackItems(): Collection
    {
        return collect([
            ['title' => 'Topics', 'href' => route('public.categories.index'), 'children' => collect()],
            ['title' => 'Content', 'href' => route('public.contents.index'), 'children' => collect()],
            ['title' => 'FAQs', 'href' => route('public.faqs.index'), 'children' => collect()],
            ['title' => 'Quizzes', 'href' => route('public.quizzes.index'), 'children' => collect()],
            ['title' => 'Services', 'href' => route('public.services.index'), 'children' => collect()],
        ]);
    }

    private function resolveMenuItemUrl(MenuItem $item): ?string
    {
        return match ($item->type) {
            'content' => $this->resolveContentTarget($item->target_reference),
            'category' => $this->resolveCategoryTarget($item->target_reference),
            'faq' => route('public.faqs.index'),
            'quiz' => $this->resolveQuizTarget($item->target_reference),
            'service_locator' => route('public.services.index'),
            'internal_route' => $this->resolveInternalRoute($item->route),
            'external_url', 'webview_page' => $this->resolveExternalTarget($item->target_reference ?: $item->route),
            default => null,
        };
    }

    private function resolveContentTarget(?string $reference): ?string
    {
        if (! Schema::hasTable('contents')) {
            return route('public.contents.index');
        }

        $id = $this->extractReferenceId($reference, 'content');

        if ($id === null) {
            return route('public.contents.index');
        }

        $slug = Content::query()->whereKey($id)->value('slug');

        return $slug === null ? route('public.contents.index') : route('public.contents.show', $slug);
    }

    private function resolveCategoryTarget(?string $reference): ?string
    {
        if (! Schema::hasTable('content_categories')) {
            return route('public.categories.index');
        }

        $id = $this->extractReferenceId($reference, 'category');

        if ($id === null) {
            return route('public.categories.index');
        }

        $slug = ContentCategory::query()->whereKey($id)->value('slug');

        return $slug === null ? route('public.categories.index') : route('public.categories.show', $slug);
    }

    private function resolveQuizTarget(?string $reference): ?string
    {
        if (! Schema::hasTable('quizzes')) {
            return route('public.quizzes.index');
        }

        $id = $this->extractReferenceId($reference, 'quiz');

        if ($id === null) {
            return route('public.quizzes.index');
        }

        $slug = Quiz::query()->whereKey($id)->value('slug');

        return $slug === null ? route('public.quizzes.index') : route('public.quizzes.show', $slug);
    }

    private function resolveInternalRoute(?string $route): ?string
    {
        if (! filled($route)) {
            return null;
        }

        if (Route::has($route)) {
            return route($route);
        }

        return $this->resolveExternalTarget($route);
    }

    private function resolveExternalTarget(?string $target): ?string
    {
        if (! filled($target)) {
            return null;
        }

        if (Str::startsWith($target, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return $target;
        }

        return url(Str::startsWith($target, ['/']) ? $target : '/'.ltrim($target, '/'));
    }

    private function extractReferenceId(?string $reference, string $prefix): ?int
    {
        if (! is_string($reference) || ! str_starts_with($reference, $prefix.':')) {
            return null;
        }

        $value = (int) str($reference)->after(':')->toString();

        return $value > 0 ? $value : null;
    }
}