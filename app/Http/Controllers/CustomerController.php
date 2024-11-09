<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Requests\Customer\DestroyRequest;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        Meta::prependTitle('Customer');
        return view("pages.customer", [
            'title' => 'Customer',
            'subtitle' => 'Kelola data customer',
        ]);
    }

    public function getCustomers(Request $request): JsonResponse
    {
        $custoemrs = Customer::when($request->has('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where('name', 'like', "%$search%");
        })
            ->paginate($request->length ?? 10);

        return response()->json($custoemrs);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            $validated['code'] = Customer::getLastCode();
            Customer::create($validated);
            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully'
            ], 201);
        }, 'Failed to create customer');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $customer = Customer::findOrFail($request->input('id'));
            $customer->update($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Customer updated successfully'
            ], 200);
        }, 'Failed to update customer');
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $customer = Customer::findOrFail($request->input('id'));
            $customer->delete();
            return response()->json([
                'status' => true,
                'message' => 'Customer deleted successfully'
            ], 200);
        }, 'Failed to delete customer');
    }

}
