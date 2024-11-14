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

    public function getMenuParentsWithChildren(): JsonResponse
    {
        $menus = $this->getParentsMenu();

        return response()->json($menus);
    }

    public function getParentsMenu()
    {
        $menus = Menu::select(
            'menus.id',
            'menus.name',
            'menus.url',
            'menus.position',
            'menus.parent_id',
            'parents.name as parent_name',
        )
            ->join('menus as parents', 'menus.parent_id', '=', 'parents.id')
            ->whereNotNull('menus.parent_id')
            ->orderBy('menus.position', 'asc')
            ->get();

        //group by parent_id, parent_name
        $menus = $menus->groupBy('parent_id')->map(function ($item) {
            return (object)[
                'id' => $item[0]->parent_id,
                'name' => $item[0]->parent_name,
                'children' => collect($item),
            ];
        })
            ->values();

        return collect($menus);
    }

    public function getParents(Request $request): JsonResponse
    {
        $parent = Menu::select('id', 'name')
                  ->whereNull('parent_id')
                  ->get();

        return response()->json($parent);
    }

    public function getUserMenus(): JsonResponse
    {
        $currentUser = auth()->user();

        $menus = Menu::select(
            'menus.id',
            'menus.name',
            'menus.url',
            'menus.position',
            'menus.parent_id',
            'parents.name as parent_name',
            'parents.position as parent_position',
        )
            ->join('menu_users', 'menus.id', '=', 'menu_users.menu_id')
            ->join('menus as parents', 'menus.parent_id', '=', 'parents.id')
            ->whereNotNull('menus.parent_id')
            ->where('menu_users.user_id', $currentUser->id)
            ->orderBy('menus.position', 'asc')
            ->get();

        //group by parent_id, parent_name
        $menus = $menus->sortBy('parent_position')->groupBy('parent_id')->map(function ($item) {
            return [
                'id' => $item[0]->parent_id,
                'name' => $item[0]->parent_name,
                'position' => $item[0]->parent_position,
                'children' => $item,
            ];
        })
            ->values();

        return response()->json($menus);
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
