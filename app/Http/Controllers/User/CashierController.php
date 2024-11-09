<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\OutletUser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\Cashier\StoreRequest;
use App\Http\Requests\User\Cashier\UpdateRequest;
use App\Models\Outlet;
use Butschster\Head\Facades\Meta;

class CashierController extends Controller
{
    public function index()
    {
        $outlets = Outlet::all();
        Meta::prependTitle('Cashier');

        return view('pages.users.cashier', [
            'title' => 'Cashier',
            'sub_title' => 'Kelola data cashier',
            'outlets' => $outlets,
        ]);
    }

    public function getCashiers(Request $request): JsonResponse
    {
        $cashiers = User::where('role', 'cashier')
            ->select(
                'users.id',
                'users.name',
                'users.username',
                'users.role',
                'outlet_users.outlet_id',
                'outlets.name as outlet_name'
            )
            ->join('outlet_users', 'users.id', '=', 'outlet_users.user_id')
            ->join('outlets', 'outlet_users.outlet_id', '=', 'outlets.id')
            ->when($request->outlet_id, function ($query) use ($request) {
                return $query->where('outlet_users.outlet_id', $request->outlet_id);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->query('search');
                return $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%$search%")
                             ->orWhere('users.username', 'like', "%$search%")
                             ->orWhere('outlets.name', 'like', "%$search%");
                });
            })
            ->paginate($request->length ?? 10);

        return response()->json($cashiers);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->safe()->except('outlet_id');
            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = 'cashier';
            $user = User::create($validated);
            //create outlet_user record
            OutletUser::create([
                'user_id' => $user->id,
                'outlet_id' => $request->outlet_id,
            ]);

            return response()->json([
                "status" => true,
                "message" => "Cashier created successfully",
            ]);
        }, 'Failed to create cashier');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->safe()->except('id', 'outlet_id', 'password');
            if ($request->has('password')) {
                $validated['password'] = Hash::make($request->password);
            }
            $user = User::findOrFail($request->input('id'));
            $user->update($validated);
            //update outlet_user record
            OutletUser::where('user_id', $user->id)->update([
                'outlet_id' => $request->outlet_id,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Successfully updated cashier'
            ]);
        }, 'Failed to update cashier');
    }
}
