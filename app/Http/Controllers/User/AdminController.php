<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\Admin\StoreRequest;
use App\Http\Requests\User\Admin\UpdateRequest;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        Meta::prependTitle('User Admin');
        return view('pages.users.admin', [
            'title' => 'Admin',
            'subtitle' => 'Kelola Data Admin',
        ]);
    }

    public function getAdmins(Request $request): JsonResponse
    {
        $admins = User::where('role', 'admin')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->query('search');
                return $query->where('name', 'like', "%$search%")
                             ->orWhere('username', 'like', "%$search%");
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
            User::create($validated);

            return response()->json([
                "status" => true,
                "message" => "User created successfully",
            ]);
        }, 'Failed to create user');
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $user = User::findOrFail($request->user_id);
            $validated = $request->safe()->except('password');
            if ($request->has('password')) {
                $validated['password'] = Hash::make($validated['password']);
            }
            $user->update($validated);
            return response()->json([
                "status" => true,
                "message" => "User updated successfully",
            ]);
        }, 'Failed to update user');
    }
}
