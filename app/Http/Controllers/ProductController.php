<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\SubBrand;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Requests\Product\DestroyRequest;

class ProductController extends Controller
{
    public function index()
    {
        Meta::prependTitle("Product");
        return view('pages.product', [
            'title' => 'Product',
            'subtitle' => 'Kelola data product',
        ]);
    }

    public function getProducts(Request $request): JsonResponse
    {
        $products = Product::select(
            'products.id',
            'products.code',
            'products.name',
            'products.class',
            'products.supplier_id',
            'suppliers.name as supplier_name',
            'products.sub_brand_id',
            'sub_brands.name as sub_brand_name',
            'sub_brands.brand_id',
            'brands.name as brand_name',
            'products.product_category_id',
            'product_categories.name as product_category_name',
            'products.buying_price',
            'products.selling_price'
        )
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->join('sub_brands', 'sub_brands.id', '=', 'products.sub_brand_id')
            ->join('brands', 'brands.id', '=', 'sub_brands.brand_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                return $query->where(function ($q) use ($search) {
                    $q->where('products.code', 'like', "%$search%")
                        ->orWhere('products.name', 'like', "%$search%")
                        ->orWhere('products.class', 'like', "%$search%")
                        ->orWhere('suppliers.name', 'like', "%$search%")
                        ->orWhere('sub_brands.name', 'like', "%$search%")
                        ->orWhere('brands.name', 'like', "%$search%")
                        ->orWhere('product_categories.name', 'like', "%$search%")
                        ->orWhere('products.buying_price', 'like', "%$search%")
                        ->orWhere('products.selling_price', 'like', "%$search%");
                });
            })
            ->when($request->sort_column && $request->sort_direction, function ($query) use ($request) {
                return $query->orderBy($request->input('sort_column'), $request->input('sort_direction'));
            })
            ->paginate($request->input("length", 10));

        return response()->json($products);
    }

    public function getSuppliers()
    {
        $suppliers = Supplier::select('id', 'name')->get();

        return response()->json($suppliers);
    }

    public function getBrands()
    {
        $brands = Brand::select('id', 'name')->get();
        return response()->json($brands);
    }

    public function getSubBrands(Request $request)
    {
        $subBrand = SubBrand::select('id', 'name')
            ->where('brand_id', $request->input('brand_id'))
            ->get();

        return response()->json($subBrand);
    }

    public function getProductCategories()
    {
        $productCategories = ProductCategory::select('id', 'name')->get();
        return response()->json($productCategories);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();

            $product = Product::create($validated);

            if ($request->hasFile('image')) {
                $imageName = 'product_img_' . $product->id . '_' . date('Ymd') . '.' . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs('images', $imageName, 'public');
                $product->update(['image' => $imagePath]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully'
            ], 201);
        }, 'Failed to create product category');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $product = Product::findOrFail($request->input('id'));
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                if ($product->image) {
                    \Storage::disk('public')->delete($product->image);
                }
                $imageName = 'product_img_' . $product->id . '_' . date('Ymd') . '.' . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs('images', $imageName, 'public');
                $validated['image'] = $imagePath;
            }

            $product->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ], 200);
        }, 'Failed to update product category');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $product = Product::findOrFail($request->input('id'));

            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully'
            ], 200);
        }, 'Failed to delete product category');
    }
}
