<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Outlet\StoreRequest;
use App\Http\Requests\Outlet\UpdateRequest;
use App\Http\Requests\Outlet\DestroyRequest;

class OutletController extends Controller
{
    public function index(Request $request): View
    {
        Meta::prependTitle('Outlet');
        return view("pages.outlet", [
            'title' => 'Outlet',
            'subtitle' => 'Kelola data outlet',
        ]);
    }

    public function getOutlets(Request $request): JsonResponse
    {
        $outlets = Outlet::when($request->has('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where('name', 'like', "%$search%");
        })
            ->paginate($request->length ?? 10);

        return response()->json($outlets);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            $validated['default_faktur_pajak'] = $request->input('default_faktur_pajak') === 'true';
            Outlet::create($validated);
            return response()->json([
                'status' => true,
                'message' => 'Outlet created successfully'
            ], 201);
        }, 'Failed to create outlet');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $outlet = Outlet::findOrFail($request->input('id'));
            $validated = $request->validated();
            $validated['default_faktur_pajak'] = $request->input('default_faktur_pajak') === 'true';
            $outlet->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'Outlet updated successfully'
            ], 200);
        }, 'Failed to update outlet');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $outlet = Outlet::findOrFail($request->input('id'));
            $outlet->delete();
            return response()->json([
                'status' => true,
                'message' => 'Outlet deleted successfully'
            ], 200);
        }, 'Failed to delete outlet');
    }
}
