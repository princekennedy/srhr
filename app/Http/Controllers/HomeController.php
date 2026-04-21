<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Support\PublicNavigation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __invoke(PublicNavigation $navigation): View
    {
        $hasCmsTables = Schema::hasTable('contents')
            && Schema::hasTable('content_categories')
            && Schema::hasTable('menus');

        return view('welcome', [
            'featuredContents' => $hasCmsTables
                ? Content::query()
                    ->with('category')
                    ->where('status', 'published')
                    ->where('visibility', 'public')
                    ->latest('published_at')
                    ->limit(6)
                    ->get()
                : new Collection(),
            'categories' => $hasCmsTables
                ? ContentCategory::query()
                    ->where('is_active', true)
                    ->withCount([
                        'contents' => fn ($query) => $query
                            ->where('status', 'published')
                            ->where('visibility', 'public'),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(6)
                    ->get()
                : new Collection(),
            'primaryMenuItems' => $navigation->items(),
        ]);
    }
}