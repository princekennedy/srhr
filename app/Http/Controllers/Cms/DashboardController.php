<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Quiz;
use App\Models\ServiceCenter;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('cms.dashboard', [
            'stats' => [
                'categories' => ContentCategory::count(),
                'contents' => Content::count(),
                'faqs' => Faq::count(),
                'quizzes' => Quiz::count(),
                'services' => ServiceCenter::count(),
                'menus' => Menu::count(),
                'menuItems' => MenuItem::count(),
                'settings' => AppSetting::count(),
            ],
            'recentContents' => Content::query()
                ->with('category')
                ->latest('updated_at')
                ->limit(6)
                ->get(),
            'moduleHighlights' => [
                [
                    'label' => 'FAQ knowledge base',
                    'count' => Faq::where('is_published', true)->count(),
                    'description' => 'Moderated answers for high-frequency SRHR questions.',
                ],
                [
                    'label' => 'Interactive quizzes',
                    'count' => Quiz::where('status', 'published')->count(),
                    'description' => 'Scenario-driven quizzes and self-check learning tools.',
                ],
                [
                    'label' => 'Service directory',
                    'count' => ServiceCenter::where('is_active', true)->count(),
                    'description' => 'Youth-friendly facilities and referral destinations.',
                ],
                [
                    'label' => 'App settings',
                    'count' => AppSetting::count(),
                    'description' => 'Runtime app labels, links, and support contact details.',
                ],
            ],
        ]);
    }
}