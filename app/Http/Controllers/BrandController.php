<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreRequest;
use App\Http\Requests\Brand\UpdateRequest;
use App\Http\Requests\Brand\DestroyRequest;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        Meta::prependTitle('Brand');
        return view("pages.brand", [
            'title' => 'Brand',
            'subtitle' => 'Kelola data Brand',
        ]);
    }

    public function getBrands(Request $request): JsonResponse
    {
        $Brands = Brand::when($request->has('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where('name', 'like', "%$search%");
        })
            ->paginate($request->length ?? 10);

        return response()->json($Brands);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            Brand::create($validated);
            return response()->json([
                'status' => true,
                'message' => 'Brand created successfully'
            ], 201);
        }, 'Failed to create Brand');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $Brand = Brand::findOrFail($request->input('id'));
            $validated = $request->validated();
            $Brand->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully'
            ], 200);
        }, 'Failed to update Brand');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $Brand = Brand::findOrFail($request->input('id'));
            $Brand->delete();
            return response()->json([
                'status' => true,
                'message' => 'Brand deleted successfully'
            ], 200);
        }, 'Failed to delete Brand');
    }
}
