<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Requests\Supplier\DestroyRequest;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        Meta::prependTitle('Supplier');
        return view("pages.supplier", [
            'title' => 'Supplier',
            'subtitle' => 'Kelola data supplier',
        ]);
    }

    public function getSuppliers(Request $request): JsonResponse
    {
        $suppliers = Supplier::when($request->has('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where('name', 'like', "%$search%");
        })
            ->paginate($request->length ?? 10);

        return response()->json($suppliers);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            Supplier::create($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Supplier created successfully'
            ], 201);
        }, 'Failed to create supplier');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $supplier = Supplier::findOrFail($request->input('id'));
            $supplier->update($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Supplier updated successfully'
            ], 200);
        }, 'Failed to update supplier');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $supplier = Supplier::findOrFail($request->input('id'));
            $supplier->delete();
            return response()->json([
                'status' => true,
                'message' => 'Supplier deleted successfully'
            ], 200);
        }, 'Failed to delete supplier');
    }
}
