<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Menu\StoreRequest;
use App\Http\Requests\Menu\UpdateRequest;
use App\Http\Requests\Menu\DestroyRequest;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        Meta::prependTitle('Menu');
        return view('pages.menu', [
          'title' => 'Menu',
          'subtitle' => 'Kelola data menu',
        ]);
    }

    public function getMenus(Request $request): JsonResponse
    {
        $menus = Menu::select(
            'menus.id',
            'menus.name',
            'menus.url',
            'menus.parent_id',
            'parents.name as parent_name',
            'menus.position',
            'menus.created_at',
        )
          ->leftJoin('menus as parents', 'menus.parent_id', '=', 'parents.id')
          ->when($request->search, function ($query, $search) use ($request) {
              $query->where('name', 'like', '%'. $search .'%');
          })
          ->orderBy('id', 'asc')
          ->paginate($request->per_page ?? 10);

        return response()->json($menus);
    }

    public function getParents(Request $request): JsonResponse
    {
        $parent = Menu::select('id', 'name')
                  ->whereNull('parent_id')
                  ->get();

        return response()->json($parent);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            Menu::create($request->validated());

            return response()->json([
              'status' => true,
              'message' => 'Menu created successfully',
            ]);
        }, "Menu created successfully");
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $menu = Menu::find($request->id);
            $menu->update($request->validated());

            return response()->json([
              'status' => true,
              'message' => 'Menu updated successfully',
            ]);
        }, "Menu updated successfully");
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            Menu::where("id", $request->id)->delete();

            return response()->json([
              'status' => true,
              'message' => 'Menu deleted successfully',
            ]);
        }, "Menu deleted successfully");
    }
}
