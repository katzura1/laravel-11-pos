<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategory\StoreRequest;
use App\Http\Requests\ProductCategory\UpdateRequest;
use App\Http\Requests\ProductCategory\DestroyRequest;

class ProductCategoryController extends Controller
{
    public function index(Request $request): View
    {
        Meta::prependTitle('Product Category');
        return view("pages.product-category", [
            'title' => 'Product Category',
            'subtitle' => 'Kelola data product category',
        ]);
    }

    public function getProductCategorys(Request $request): JsonResponse
    {
        $suppliers = ProductCategory::when($request->has('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where('name', 'like', "%$search%");
        })
            ->paginate($request->length ?? 10);

        return response()->json($suppliers);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            ProductCategory::create($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Product Category created successfully'
            ], 201);
        }, 'Failed to create product category');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $productCategory = ProductCategory::findOrFail($request->input('id'));
            $productCategory->update($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Product Category updated successfully'
            ], 200);
        }, 'Failed to update product category');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $productCategory = ProductCategory::findOrFail($request->input('id'));
            $productCategory->delete();
            return response()->json([
                'status' => true,
                'message' => 'Product Category deleted successfully'
            ], 200);
        }, 'Failed to delete product category');
    }
}
