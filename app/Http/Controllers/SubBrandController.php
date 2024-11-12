<?php

namespace App\Http\Controllers;

use App\Models\SubBrand;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubBrand\StoreRequest;
use App\Http\Requests\SubBrand\UpdateRequest;
use App\Http\Requests\SubBrand\DestroyRequest;

class SubBrandController extends Controller
{
    public function index(Request $request): View
    {
        Meta::prependTitle('SubBrand');
        return view("pages.sub-brand", [
            'title' => 'SubBrand',
            'subtitle' => 'Kelola data SubBrand',
        ]);
    }

    public function getSubBrands(Request $request): JsonResponse
    {
        $SubBrands = SubBrand::select(
            'sub_brands.id',
            'sub_brands.name',
            'sub_brands.brand_id',
            'brands.name as brand_name',
            'sub_brands.created_at',
        )
          ->join('brands', 'brands.id', '=', 'sub_brands.brand_id')
          ->when($request->has('search'), function ($query) use ($request) {
              $search = $request->input('search');
              return $query->where(function ($q) use ($search) {
                  $q->where('sub_brands.name', 'like', "%$search%")
                    ->orWhere('brands.name', 'like', "%$search%");
              });
          })
            ->paginate($request->length ?? 10);

        return response()->json($SubBrands);
    }

    public function getBrands(Request $request): JsonResponse
    {
        $Brands = Brand::select('id', 'name')->get();
        return response()->json($Brands);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            SubBrand::create($validated);
            return response()->json([
                'status' => true,
                'message' => 'SubBrand created successfully'
            ], 201);
        }, 'Failed to create SubBrand');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $SubBrand = SubBrand::findOrFail($request->input('id'));
            $validated = $request->validated();
            $SubBrand->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'SubBrand updated successfully'
            ], 200);
        }, 'Failed to update SubBrand');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $SubBrand = SubBrand::findOrFail($request->input('id'));
            $SubBrand->delete();
            return response()->json([
                'status' => true,
                'message' => 'SubBrand deleted successfully'
            ], 200);
        }, 'Failed to delete SubBrand');
    }
}
