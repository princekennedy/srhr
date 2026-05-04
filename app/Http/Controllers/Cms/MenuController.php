<?php

namespace App\Http\Controllers\Cms;

use App\Enums\MenuLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\MenuRequest;
use App\Models\Menu;
use App\Models\Slider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('cms.menus.index', [
            'menus' => Menu::query()
                ->with('slider')
                ->withCount('items')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('cms.menus.create', [
            'menu' => new Menu(),
            'visibilityOptions' => Menu::VISIBILITY_OPTIONS,
            'layoutOptions' => MenuLayoutType::options(),
            'sliderOptions' => Slider::query()->where('is_active', true)->orderBy('sort_order')->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(MenuRequest $request): RedirectResponse
    {
        $menu = Menu::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.menus.edit', $menu)
            ->with('status', 'Menu created. Add items to define the app navigation.');
    }

    public function show(Menu $menu): RedirectResponse
    {
        return redirect()->route('cms.menus.edit', $menu);
    }

    public function edit(Menu $menu): View
    {
        $menuItems = Menu::query()->items()
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->filter(fn (Menu $item) => $item->belongsToMenu($menu))
            ->values();

        return view('cms.menus.edit', [
            'menu' => $menu,
            'menuItems' => $menuItems,
            'visibilityOptions' => Menu::VISIBILITY_OPTIONS,
            'layoutOptions' => MenuLayoutType::options(),
            'sliderOptions' => Slider::query()->where('is_active', true)->orderBy('sort_order')->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(MenuRequest $request, Menu $menu): RedirectResponse
    {
        $menu->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.menus.edit', $menu)
            ->with('status', 'Menu updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('cms.menus.index')
            ->with('status', 'Menu deleted.');
    }
}