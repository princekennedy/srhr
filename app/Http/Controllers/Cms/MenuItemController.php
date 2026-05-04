<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\MenuItemRequest;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;

class MenuItemController extends Controller
{
    public function create(Menu $menu): RedirectResponse
    {
        return redirect()->route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children', 'item' => 'create']);
    }

    public function store(MenuItemRequest $request, Menu $menu): RedirectResponse
    {
        Menu::query()->items()->create(Menu::normalizeForPersistence([
            ...$request->validated(),
            'parent_id' => $request->filled('parent_id') ? $request->integer('parent_id') : $menu->id,
            'sort_order' => $request->integer('sort_order'),
            'open_in_webview' => $request->boolean('open_in_webview'),
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()
            ->route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children'])
            ->with('status', 'Menu item created.');
    }

    public function edit(Menu $menu, Menu $item): RedirectResponse
    {
        abort_unless($item->belongsToMenu($menu), 404);

        return redirect()->route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children', 'item' => $item->id]);
    }

    public function update(MenuItemRequest $request, Menu $menu, Menu $item): RedirectResponse
    {
        abort_unless($item->belongsToMenu($menu), 404);

        $item->update(Menu::normalizeForPersistence([
            ...$request->validated(),
            'parent_id' => $request->filled('parent_id') ? $request->integer('parent_id') : $menu->id,
            'sort_order' => $request->integer('sort_order'),
            'open_in_webview' => $request->boolean('open_in_webview'),
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()
            ->route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children', 'item' => $item->id])
            ->with('status', 'Menu item updated.');
    }

    public function destroy(Menu $menu, Menu $item): RedirectResponse
    {
        abort_unless($item->belongsToMenu($menu), 404);

        $item->delete();

        return redirect()
            ->route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children'])
            ->with('status', 'Menu item deleted.');
    }
}