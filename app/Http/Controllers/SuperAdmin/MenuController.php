<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\AccessMenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function index(): View
    {
        return view('super-admin.menu.index', [
            'parents' => $this->accessMenu->getParentMenus(),
            'menuTree' => $this->accessMenu->getAllMenusWithDisplay(),
            'allMenus' => $this->accessMenu->getAllGlobalMenus(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:pages_menu_list,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'display_in_menu' => ['nullable', 'in:0,1,true,false'],
        ]);

        if (isset($validated['display_in_menu'])) {
            $validated['display_in_menu'] = in_array($validated['display_in_menu'], [1, '1', true], true) ? 1 : 0;
        }

        try {
            $menu = $this->accessMenu->addMenu(null, auth()->user(), $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu added successfully.',
                'menu_id' => $menu->id,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:pages_menu_list,id'],
            'parent_id' => ['nullable', 'integer', 'exists:pages_menu_list,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'display_in_menu' => ['nullable', 'in:0,1,true,false'],
        ]);

        if (isset($validated['display_in_menu'])) {
            $validated['display_in_menu'] = in_array($validated['display_in_menu'], [1, '1', true], true) ? 1 : 0;
        }

        try {
            $this->accessMenu->updateMenu((int) $validated['menu_id'], $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu updated successfully.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:pages_menu_list,id'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:pages_menu_list,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $this->accessMenu->reorderMenus(null, $validated['items']);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu order saved.',
        ]);
    }

    public function updateDisplay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:pages_menu_list,id'],
            'display' => ['required', 'boolean'],
        ]);

        $this->accessMenu->updateMenuDisplay($validated['menu_id'], (bool) $validated['display']);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu visibility updated.',
        ]);
    }

    public function storeButton(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:pages_menu_list,id'],
            'button_title' => ['required', 'string', 'max:255'],
            'button_link' => ['required', 'string', 'max:255'],
            'button_status' => ['nullable', 'boolean'],
        ]);

        $this->accessMenu->addButton(
            $validated['menu_id'],
            auth()->user(),
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Button added successfully.',
        ]);
    }

    public function destroyButton(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'button_id' => ['required', 'integer', 'exists:pages_buttons,id'],
        ]);

        $this->accessMenu->deleteButton($validated['button_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Button deleted successfully.',
        ]);
    }
}
