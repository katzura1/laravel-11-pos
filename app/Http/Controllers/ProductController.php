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
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Requests\Product\DestroyRequest;
use Intervention\Image\ImageManagerStatic as Image;

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
            'products.selling_price',
            'products.image'
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
            ->paginate($request->input("length", 10))
            ->toArray();

        //mapping products image and replace with url
        $products['data'] = array_map(function ($product) {
            $product['image'] = $product['image'] ? url('storage/' . $product['image']) : null;
            return $product;
        }, $products['data']);

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
            $validated = $request->safe()->except('image');

            $product = Product::create($validated);

            if ($request->has('image')) {
                $imageData = $request->input('image');
                $imageSize = (int) (strlen(rtrim($imageData, '=')) * 3 / 4); // Calculate the size of the base64 image
                if ($imageSize > 2097152) { // 2MB in bytes
                    return response()->json([
                        'status' => false,
                        'message' => 'Image size exceeds the maximum limit of 2MB'
                    ], 422);
                }
                $imageName = 'product_img_' . $product->id . '_' . date('Ymd') . '.webp';
                $imagePath = 'images/' . $imageName;
                $fileImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                $image = \Image::read($fileImage)->toWebp(60);
                Storage::disk('public')->put($imagePath, (string) $image);
                $product->update(['image' => $imagePath]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully'
            ], 201);
        }, 'Failed to create product');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $product = Product::findOrFail($request->input('id'));
            $validated = $request->safe()->except('image');

            if ($request->has('image')) {
                $imageData = $request->input('image');
                $imageSize = (int) (strlen(rtrim($imageData, '=')) * 3 / 4); // Calculate the size of the base64 image
                if ($imageSize > 2097152) { // 2MB in bytes
                    return response()->json([
                        'status' => false,
                        'message' => 'Image size exceeds the maximum limit of 2MB'
                    ], 422);
                }
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imageName = 'product_img_' . $product->id . '_' . date('Ymd') . '.webp';
                $imagePath = 'images/' . $imageName;
                $fileImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                $image = \Image::read($fileImage)->toWebp(60);
                Storage::disk('public')->put($imagePath, (string) $image);
                $validated['image'] = $imagePath;
            }

            $product->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ], 200);
        }, 'Failed to update product');
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
        }, 'Failed to delete product');
    }
}
