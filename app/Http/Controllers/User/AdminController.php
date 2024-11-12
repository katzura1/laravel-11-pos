<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\StoreOutletRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\Admin\StoreRequest;
use App\Http\Requests\User\Admin\UpdateRequest;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        Meta::prependTitle('User Admin');
        $outlets = Outlet::all();

        return view('pages.users.admin', [
            'title' => 'Admin',
            'subtitle' => 'Kelola Data Admin',
            'outlets' => $outlets,
        ]);
    }

    public function getAdmins(Request $request): JsonResponse
    {
        $admins = User::select(
            'users.id',
            'users.name',
            'users.username',
            'users.created_at',
            'users.role',
        )
            ->with([
                'outletUser:id,user_id,outlet_id',
                'outletUser.outlet:id,name',
            ])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->query('search');
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                    $q->orWhere('username', 'like', "%$search%");

                });
            })
            ->paginate($request->length ?? 10);

        return response()->json($admins);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = 'admin';
            $user = User::create($validated);
            //add outlet user
            $user->outletUser()->createMany(array_map(function ($outlet_id) {
                return ['outlet_id' => $outlet_id];
            }, $request->outlet_id));

            return response()->json([
                "status" => true,
                "message" => "User created successfully",
            ]);
        }, 'Failed to create user');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $user = User::findOrFail($request->id);
            $validated = $request->safe()->except('password');
            if ($request->has('password')) {
                $validated['password'] = Hash::make($request->password);
            }
            $user->update($validated);
            return response()->json([
                "status" => true,
                "message" => "User updated successfully",
            ]);
        }, 'Failed to update user');
    }

    public function storeOutlet(StoreOutletRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            $user = User::findOrFail($validated['user_id']);
            $user->outletUser()->delete();
            $user->outletUser()->createMany(array_map(function ($outlet_id) {
                return ['outlet_id' => $outlet_id];
            }, $validated['outlet_id']));

            return response()->json([
                "status" => true,
                "message" => "Outlet assigned successfully",
            ]);
        }, 'Failed to assign outlet');
    }
}
