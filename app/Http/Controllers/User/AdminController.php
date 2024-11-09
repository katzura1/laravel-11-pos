<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
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
        return view('pages.users.admin', [
            'title' => 'Admin',
            'subtitle' => 'Kelola Data Admin',
        ]);
    }

    public function getAdmins(Request $request): JsonResponse
    {
        $admins = User::where('role', 'admin');

        if ($request->has('search')) {
            $search = $request->query('search');
            $admins = $admins->where('name', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%");
        }

        $length = $request->input("length", 10);
        $admins = $admins->paginate($length);

        return response()->json($admins);
    }


    public function store(StoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $password = $request->post("password");
            $password = Hash::make($password);
            $request = $request->merge(["password" => $password]);

            $validated = $request->validated();
            $validated['role'] = 'admin';
            // dd($validated);
            User::create($validated);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "User created successfully",
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create user'
            ], 500);
        }
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($request->input('id'));
            if ($request->has('password')) {
                $password = $request->post("password");
                $password = Hash::make($password);
                $request = $request->merge(["password" => $password]);
            }
            $user->update($request->except('password'));
            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "User updated successfully",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user'
            ], 500);
        }
    }
}
